$(function(){
  const MSG_EMPTY = '必須項目です。';
  const MSG_NAME_MAX = '50文字以内に入力してください';
  const MSG_PROFILE_MAX = '160文字以内に入力してください';

  $(".fn-profileName").keyup(function(){

    var count = this.value.length;　
    var counterNode = document.querySelector('.counter-number1');
    counterNode.innerText = count;

    var form_g = $(this).closest('.form-group');
    if($(this).val().length === 0){
      form_g.removeClass('is-success').addClass('is-err');
      form_g.find('.is-areaMsg').text(MSG_EMPTY);
    }else if($(this).val().length > 50){
      form_g.removeClass('is-success').addClass('is-err');
      form_g.find('is-areaMsg').text(MSG_NAME_MAX);
    }else{
      form_g.removeClass('is-err').addClass('is-success');
      form_g.find('.is-areaMsg').text('');
    }
  });
  $(".fn-profileProfile").keyup(function(){
    var count = this.value.length;　
    var counterNode = document.querySelector('.counter-number2');
    counterNode.innerText = count;

    var form_g = $(this).closest('.form-group');
    if($(this).val().length === 0){
      form_g.removeClass('is-success').addClass('is-err');
      form_g.find('.is-areaMsg').text(MSG_EMPTY);
    }else if($(this).val().length > 160){
      form_g.removeClass('is-success').addClass('is-err');
      form_g.find('.is-areaMsg').text(MSG_PROFILE_MAX);
    }else{
      form_g.removeClass('is-err').addClass('is-success');
      form_g.find('.  is-areaMsg').text('');
    }
  });
});
