/**
 *
 * @type {number}
 */
let
	/**
	 *
	 * @type {number}
	 */
	counter = 0,
	/**
	 *
	 * @type {string[]}
	 */
	FILES = [],
	/**
	 *
	 * @type {string[]}
	 */
	SUCCESS_FILES = [],
	/**
	 *
	 * @type {string[]}
	 */
	ERROR_FILES = [],
	/**
	 *
	 * @type {number}
	 */
	TOTAL_FILES = 0;

const
	fs = require('fs'),
 	glob = require('glob'),
	{series} = require('async'),
	{exec} = require('child_process'),
	/**
 *
 * @param path
 * @returns {Boolean}
 */
	isFile = path => fs.statSync(path).isFile(),
	/**
 *
 * @param path
 * @returns {Boolean}
 */
	isDirectory = path => fs.statSync(path).isDirectory(),
	/**
 *
 * @param path
 * @returns {string[]}
 */
	getPath = path => glob.sync(`${path}/**/*`),
	/**
 *
 * @param path
 * @returns {string[]}
 */
	getFiles = path => getPath(path).filter(path => isFile(path)),
	/**
 *
 * @param path
 * @returns {string[]}
 */
	getDirectories = path => getPath(path).filter(path => isDirectory(path)),
	/**
	 *
	 * @param path
	 * @param excludes
	 * @returns {string[]}
	 */
	getDirectory = (path, excludes = []) => getFiles(path).filter(file => excludes.indexOf(file) === -1),
	/**
	 *
	 * @param {string} filePath
	 */
	truncateFileSync = (filePath) => fs.truncateSync(filePath, 0),
	/**
	 *
	 * @param command
	 * @param params
	 * @returns {*}
	 */
	execCommand = (command, params = '') => series([() => exec(command + (params ? ` ${params}` : ''))]),
	/**
	 *
	 * @param filePath
	 * @returns {string}
	 */
	readClass = (filePath) => String(fs.readFileSync(filePath)).replace('export default class', 'export class').replace(/^import.*$/gm, ''),
	/**
	 *
	 * @param {string} targetFile
	 * @param {string} filePath
	 * @param {function} completeHandler
	 */
	appendFile = (targetFile, filePath, completeHandler) =>
	{
		let fileData = null;

		try
		{
			fileData = readClass(filePath)
		}
		catch (e)
		{
			console.log(`${++counter}. ${e.path} error.`);
			ERROR_FILES.push(e.path);
			completeHandler();
			return;
		}

		fs.writeFile(targetFile, fileData, { flag: 'a+' }, (error) =>
		{
			if (error) throw error;
			else console.log(`${++counter}. ${filePath} success.`);
			SUCCESS_FILES.push(filePath);
			completeHandler();
		});
	},
	/**
	 *
	 * @param {string} targetFile
	 * @param {function} completeHandler
	 */
	appendFiles = (targetFile, completeHandler = () => {}) =>
	{
		const file = FILES.shift();
		if(file) appendFile(targetFile, file, () => { appendFiles(targetFile, completeHandler); });
		else completeHandler();
	},
	/**
	 *
	 * @returns {string[]}
	 */
	getBaseClasses = () =>
	{
		return [
			`${BASE_PATH}/Data/EventDispatcher.js`,
			`${BASE_PATH}/Data/StaticEventDispatcher.js`,
			`${BASE_PATH}/DynamicObject.js`,
			`${BASE_PATH}/DOM/DOMComponent.js`,
			`${BASE_PATH}/Net/API.js`
		];
	},
	/**
	 *
	 * @returns {string[]}
	 */
	getComponentsBaseClasses = () =>
	{
		return [
			`${BASE_PATH}/DOM/Input.js`,
			`${BASE_PATH}/DOM/InputNumber.js`
		];
	},
	/**
	 *
	 * @param {string} targetFile
	 * @param {function} completeHandler
	 */
	concatFiles = (targetFile, completeHandler) =>
	{
		truncateFileSync(targetFile);

		appendFiles(targetFile, () =>
		{
			console.info(`\n${SUCCESS_FILES.length} of ${TOTAL_FILES} files copy success.`);
			console.info(`${ERROR_FILES.length} of ${TOTAL_FILES} files copy error.\n`);
			completeHandler();
		});
	},
	/**
	 * @param {string} targetFile
	 * @returns {*}
	 */
	minifyFile = (targetFile) => execCommand(`uglifyjs`, `${targetFile} -o ${targetFile.replace('.js', '.min.js')}`),
	/**
	 *
	 * @param {string} targetFile
	 * @param {string[]} files
	 * @param {function} completeHandler
	 */
	concatClasses = (targetFile, files, completeHandler = () => {}) =>
	{
		FILES = files;
		TOTAL_FILES = FILES.length;
		console.clear();
		concatFiles(targetFile, () =>
		{
			minifyFile(targetFile);
			console.info(`${targetFile.replace('.js', '.min.js')} generated\n`);
			completeHandler();
		});

		console.log('arguments', process.argv.slice(2));
	},
	/**
	 *
	 * @type {string}
	 */
	BASE_PATH = './object';
/**
 * run command
 */
concatClasses
(
	`./classes.js`,
	[
		...getDirectory(`${BASE_PATH}/Data`, [`${BASE_PATH}/Data/EventDispatcher.js`, `${BASE_PATH}/Data/StaticEventDispatcher.js`]),
		...getDirectory(`${BASE_PATH}/Util`),
		...getBaseClasses(),
		...getDirectory(`${BASE_PATH}/Net`, [`${BASE_PATH}/Net/API.js`]),
		...getComponentsBaseClasses(),
		...getDirectory(`${BASE_PATH}/DOM`, [`${BASE_PATH}/DOM/Input.js`, `${BASE_PATH}/DOM/InputNumber.js`, `${BASE_PATH}/DOM/DOMComponent.js`])
	]
);
