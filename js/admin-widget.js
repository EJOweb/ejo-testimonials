jQuery(document).ready(function() {

	widget_view_settings_sortable();

});

jQuery(document).on('widget-updated widget-added', function(e, widget){
	
    widget_view_settings_sortable();

});

function widget_view_settings_sortable()
{
	jQuery( "table.view_settings tbody" ).sortable({
		revert: true,
		handle: ".ejo-move",
	});
}