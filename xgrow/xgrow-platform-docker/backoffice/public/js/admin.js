'use strict';
//import Form from './object/DOM/Form.js';
import { Form } from '../js/classes.min.js';

const log = console.log;

const start = () =>
{
	const form = new Form('#create-admin-form', false);

	$(window).on('form:submit', (e) => { log('submited'); });
	log('admin started');
};

$(document).ready(start);
