
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

$('.delete-modal').on('hidden.bs.modal', function() {
	$('#delete-form').attr('action', '');
});

$('.delete-button').click(function() {
	$('#delete-form').submit();
});

$('.thumbnail').mouseenter(function() {
	$(this).find('.play').css('opacity', '1');
});

$('.thumbnail').mouseleave(function() {
	$(this).find('.play').css('opacity', '0.5');
});

$('.start-campaign').click(function() {
	let campaign_id = $(this).data('action');
	let url = '/' + admin_route + '/campaigns/' + campaign_id + '/start';

	axios.get(url)
		 .then((response) => {
		 	let campaign_id = response.data.id;
			let buttons = $('table').find("[data-action='" + campaign_id + "']");
			buttons.filter('.campaign-action').hide();
			buttons.filter('.stop-campaign').show();
			buttons.filter('.pause-campaign').show();
			let icons = $('table').find("[data-status='" + campaign_id + "']");
			icons.filter('.campaign-status').hide();
			icons.filter('.running-status-icon').show();
		 })
		 .catch((error) => console.log(error));

});

$('.stop-campaign').click(function() {
	let campaign_id = $(this).data('action');
	let buttons = $('table').find("[data-action='" + campaign_id + "']");
	buttons.filter('.campaign-action').hide();
	buttons.filter('.start-campaign').show();
	let icons = $('table').find("[data-status='" + campaign_id + "']");
	icons.filter('.campaign-status').hide();
	icons.filter('.stopped-status-icon').show();
});

$('.pause-campaign').click(function() {
	let campaign_id = $(this).data('action');
	let buttons = $('table').find("[data-action='" + campaign_id + "']");
	buttons.filter('.campaign-action').hide();
	buttons.filter('.resume-campaign').show();
	let icons = $('table').find("[data-status='" + campaign_id + "']");
	icons.filter('.campaign-status').hide();
	icons.filter('.paused-status-icon').show();
});

$('.resume-campaign').click(function() {
	let campaign_id = $(this).data('action');
	let buttons = $('table').find("[data-action='" + campaign_id + "']");
	buttons.filter('.campaign-action').hide();
	buttons.filter('.stop-campaign').show();
	buttons.filter('.pause-campaign').show();
	let icons = $('table').find("[data-status='" + campaign_id + "']");
	icons.filter('.campaign-status').hide();
	icons.filter('.running-status-icon').show();
});