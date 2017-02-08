
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app'
});

$('.delete-resource').click(function() {
	$('#delete-form').attr('action', $(this).data('url'));
	$('.delete-modal').modal('show');
});

$('.delete-modal').on('hidden.bs.modal', function(){
	$('#delete-form').attr('action', '');
});

$('.delete-button').click(function(){
	$('#delete-form').submit();
});

$('.thumbnail').mouseenter(function(){
	$(this).find('.play').css('opacity', '1');
});

$('.thumbnail').mouseleave(function(){
	$(this).find('.play').css('opacity', '0.5');
});