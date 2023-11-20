'use strict';
//import AjaxForm from './object/DOM/AjaxForm.js';
import { AjaxForm } from '../js/classes.min.js';

window.log = console.log;

console.clear();

const start = () =>
{
	log('>>> administrators started <<<')
	const form = new AjaxForm('#create-form', { strictResponse:true });
};

$(document).ready(() => { start(); });
