<?php

return [


	'section' => [

		'order' => [

			//1 => 'Recomendados',
		    2 => 'Últimos publicados',
		    3 => 'Primeiros publicados',
		    4 => 'Mais likes',
		    5 => 'Menos likes',
		    6 => 'Mais acessados',
		    7 => 'Menos acessados',
		    8 => 'Mais comentados',
		    9 => 'Menos comentados'

		],

		'status' => [
			0 => 'Inativo',
		    1 => 'Ativo'
		],

        'qtd_per_page' => [5,6,8,15, 30]

    ],

	'course' => [
		'certificate' => [
			1 => 'Padrão',
		],

        'form_delivery' => [
            1 => 'Sequencial',
            2 => 'Programada'
        ],

        'delivery_model' => [
            1 => 'ultimo módulo',
            2 => 'ultima aula'
        ],

        'delivery_date' => [
            1 => 'da data de início do curso',
            2 => 'de data específica',
            3 => 'de quando assinante iniciar o curso',
        ]
    ],

    'type_file' => [
    	'image' => 'jpeg,jpg,png,gif',
		'document' => 'pdf, doc, docx, ppt, pptx, xls, xlsx, word',
		'favicon' => 'ico',
    ],

    'imgTemplatesDir' => env('CONSTANTS_IMGTEMPLATESDIR', 'http://gestao.fandone.com.br/uploads/'),

    'emailSupport' => [

        'reasonContact' => [
            1 => 'Problema técnico',
            2 => 'Dúvida de uso',
            3 => 'Sugestão de recurso',
            4 => 'Plataforma fora do ar',
            5 => 'Mensagem',
            6 => 'Recuperação de senha'
        ],

        'from' => env('MAIL_FROM_ADDRESS','no-reply@xgrow.com.br'),

        'subject' => 'FANDONE SUPORTE'
    ],

    'menu' => [
    	'types' => [
    		1 => 'Seção',
    		2 => 'Curso',
    		3 => 'Conteúdo',
            4 => 'Agrupamento',
            5 => 'Fórum',
    	],
    ],

    'content' => [
        'types' => [
            1 => 'Seção',
            2 => 'Curso',
            3 => 'Conteúdo',
            4 => 'Agrupamento',
        ],
    ],


    'widget' => [
                   //organizado por ordem de exibição na página platform-config
        'types' => [
           ['id' => 1, 'id' => 1, 'has_title' => true, 'has_content' => true, 'name' => 'Seção', 'allow_all' => false, 'has_amount' => true, 'has_image' => false, 'content_type' => 1],
           ['id' => 2, 'has_title' => true, 'has_content' => true, 'name' => 'Conteúdo', 'allow_all' => false, 'has_amount' => false, 'has_image' => false, 'content_type' => 3],
           ['id' => 5, 'has_title' => true, 'has_content' => true, 'name' => 'Cursos', 'allow_all' => true,'has_amount' => false, 'has_image' => false, 'content_type' => 2],
           ['id' => 3, 'has_title' => false, 'has_content' => false, 'name' => 'Imagem', 'allow_all' => false, 'has_amount' => false, 'has_image' => true, 'content_type' => 3],
           ['id' => 6, 'has_title' => true, 'has_content' => true, 'name' => 'Agrupamento', 'allow_all' => false,'has_amount' => true, 'has_image' => false, 'content_type' => 4],
           ['id' => 4, 'has_title' => true, 'has_content' => true, 'name' => 'Últimos Conteúdos', 'allow_all' => true,'has_amount' => true, 'has_image' => false, 'content_type' => 1],
        ],

        'fonts' => ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Impact', 'Palatino', 'Symbol', 'Times New Roman', 'Trebuchet MS', 'Verdana', 'Webdings']
    ],

    'email_areas' => [
        1 => 'Sistema',
        2 => 'Planos',
        3 => 'Login',
        4 => 'Financeiro',
    ],

];
