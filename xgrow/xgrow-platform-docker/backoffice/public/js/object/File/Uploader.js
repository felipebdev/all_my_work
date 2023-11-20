/**
 *
 */
FilePond.registerPlugin(FilePondPluginImagePreview);
FilePond.registerPlugin(FilePondPluginFileRename);
$.fn.filepond.registerPlugin(FilePondPluginImagePreview);
/**
 * @class Uploader
 */
export default class Uploader
{
	/**
	 * Uploader constructor
	 * @param {JQuery} selector
	 * @param {Object} options
	 */
	constructor(selector, options = {urlParam: ''})
	{
		this._$element = $(selector);
		const server =
		{
			url: '/profile/upload/',
			process:
			{
				url: `${options.urlParam || ''}`,
				method: 'POST',
				headers: { 'X-CSRF-TOKEN': window.csrfToken },
				withCredentials: true,
				onload: (response) =>
				{
					response = JSON.parse(response);
					this._$element.attr('data-url', response.image || '');
					if(options.onResponse) options.onResponse(response);
				},
				onerror: (error) =>
				{
					if(options.onError) options.onError(error);
				}
			},
			fetch:
			{
				method: 'GET',
				headers: { 'X-CSRF-TOKEN': window.csrfToken },
				withCredentials: true,
				url:`${options.urlParam || ''}`
			},
			revert:
			{
				method: 'DELETE',
				headers: { 'X-CSRF-TOKEN': window.csrfToken },
				withCredentials: true,
				url:`${options.urlParam || ''}`
			}
		};
		// Turn input element into a pond
		this._$filePond = $(selector).filepond
		(
			{
				...{
					server,
					storeAsFile: true,
					labelIdle: 'Arraste e solte seus arquivos ou <span class = "filepond"> Navegar </span>',
					labelFileProcessingComplete: 'envio finalizado',
					labelFileProcessingAborted: 'envio cancelado',
					labelTapToUndo: '',
					labelFileProcessing: 'enviando',
					labelTapToCancel: '',
					labelFileProcessingError: 'erro durante o envio',
					labelTapToRetry: 'tente novamente mais tarde'
				},
				...options
			}
		).filepond('allowMultiple', false).on('FilePond:addfile', function (e)
		{
			if(options.onAddFile) options.onAddFile(e);
		}).on('FilePond:processfile', function (e)
		{
			if(options.uploadFileCallback) options.uploadFileCallback(e);
		});

		/*
		 $(selector).filepond('addFile', '/path/file.extension').then(function(file){
		 console.log('file added', file);
		 });
		 */

	}
	removeFiles()
	{
		this._$filePond.filepond('removeFiles');
	}
	val()
	{
		return this._$element.attr('data-url');
	}
}
