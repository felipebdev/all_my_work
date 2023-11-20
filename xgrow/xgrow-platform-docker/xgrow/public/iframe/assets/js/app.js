const logged = window.localStorage.getItem('logged');
const token = window.localStorage.getItem('token');
const subscriber_name = window.localStorage.getItem('subscriber_name');
const root = window.location.protocol + "//" + window.location.host + "/";
const folder = getLevel();

const {mode, url_web, id: platform_id} = platform;
const url_api = `${url_web}api/`;
const img_path = `${url_web}uploads/`;
const url_template = window.location.protocol + '//' + window.location.host;

const goToWelcome = () => self.location = `${folder}welcome.html`;
const goToHome = () => self.location = `${folder}index.html`;

const logInto = () => {
    const email = $('#email').val();
    const password = $('#password').val();
    const validations = [];
    validations.push([email && email.includes("@"), "Email incorreto"]);
    validations.push([password && password.length >= 5, "Senha deve ter ao menos 5 caracteres"]);

    loadingIcon('button-singin', 'show');
    const errors = checkValid(validations);
    if (errors !== undefined) {
        
        alertError(errors[1])
        loadingIcon('button-singin', 'hide');
    }
    else {
        const form = $('#form-login');
        const data_to_login = form.serialize() + '&platform_id=' + platform_id + "&route=login"; // Fernanda
        checkLogin (data_to_login, response => {
            const { error, message } = response
            if (error === undefined) {
                LoginWithSuccess(response);
            }
            else {
                alertError(message);
                loadingIcon('button-singin', 'hide');
            }
        });
    }
}


const logOut = () => {
    window.localStorage.removeItem('token');
    window.localStorage.removeItem('name');
    window.localStorage.removeItem('logged');
    const route = 'logout';
    setLogOut(route,goToHome,goToHome)
}


const LoginWithSuccess = response => {
    const { token, subscriber: { name } } = response
    window.localStorage.setItem('token', token);
    window.localStorage.setItem('subscriber_name', name);
    window.localStorage.setItem('logged', true);
    goToWelcome();
}

const alertError = message => {
    $('.alert').removeClass('d-none alert-success');
    $('.alert').html(message);
    $('.alert').addClass('show alert-danger');
}

const alertSuccess = message => {
    $('.alert').removeClass('d-none alert-danger');
    $('.alert').html(message);
    $('.alert').addClass(`show alert-success`);
}

function getLevel(){
    const title = document.title;
    let folder = (title != 'index') ? "../": "";
    /*
    switch (title) {
        case 'section':
            folder = "../"
            break;
    }
    */
    return folder;
}

function checkValid(validations) {
    return validations.find(condition => (!condition[0]));
}

const loadingIcon = (btn, action) => {
    const icon = '<i class="fa fa-spinner fa-spin"></i>';
    const button = $(`#${btn}`);
    const text = button.text();
    let content = text;
    if (action == 'show') {
        button.prop("disabled", true);
        content += ` ${icon}`;
    }
    else if (action == 'hide') {
        button.prop("disabled", false);
    }
    button.html(content);
}

const slugSectionByModeEnv = (slug_section, template_id = 0, template_folder)  => {
    let slug = slug_section;
    if(mode == 'templates-base'){
        if(template_id == 0){
            slug =  section.name_slug;
        }
        else{
            slug = template_folder;
        }
    }
    return slug;
}



function setSeoTags(response){
        
        const {seo_description, seo_title, seo_keywords, favicon_filename} = response;

        html = `
            <meta name="description" content="${seo_description}">
            <meta name="keywords" content="${seo_keywords}">
        `;
    
        $(html).insertAfter($("[charset=UTF-8]"));
    
        $("title").text(seo_title);
    
        favicon = `
            <link rel="shortcut icon" href="${img_path}/${favicon_filename}" />
        
        `;
    
        $(favicon).insertBefore("title");
               
}

/*
window.onerror = function (msg, url, linenumber) {
    alert('Error message: ' + msg + '\nURL: ' + url + '\nLine Number: ' + linenumber);
    return true;
}
$.ajaxSetup({
    error: function (xhr) {
        //alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        alert(xhr.status);
        if (xhr.status == 401) {
            alert(xhr.status);
            logout_redirect();
        }
        dump(xhr, 'body')
    }
});
*/