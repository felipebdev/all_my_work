'use strict';
import { AjaxForm } from './classes.min.js';

window.log = console.log;

console.clear();

const start = () =>
{
	log('>>> users started <<<')
	const form = new AjaxForm('#create-form', { strictResponse:true });
};

$(document).ready(() => { start(); });
