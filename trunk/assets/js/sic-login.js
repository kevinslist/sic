$(document).ready(sic_init_login);

function sic_init_login(){
   $('#kb-athentication-provider-wrapper').on('kb-login-user-authenticated', sic_login_user_authenticated);
}

function sic_login_user_authenticated(e, params){
  params[kb_csrf_name] = kb_csrf_hash;
  $.ajax({
    url: app_home + 'check-registration',
    dataType: 'json',
    data: params,
    type: 'POST',
    success: sic_login_user_authenticated_response,
    error: sic_login_user_authenticated_response
  });
}

function sic_login_user_authenticated_response(r){
  if(r.status){ location.reload(); }
}