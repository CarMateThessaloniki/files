(function ()
{
	// create kleoShortcodes plugin
	tinymce.create("tinymce.plugins.kleoShortcodes",
	{
		init: function ( ed, url )
		{
			ed.addCommand("kleoPopup", function ( a, params )
			{
				var popup = params.identifier;
				
				// load thickbox
				tb_show("Insert Shortcode", url + "/popup.php?popup=" + popup + "&width=" + 800);
			});
		},
		createControl: function ( btn, e )
		{
			if ( btn == "kleo_button" )
			{	
				var a = this;
				
				var btn = e.createSplitButton('kleo_button', {
                    title: "Insert Shortcode",
					image: KleoShortcodes.plugin_folder +"/tinymce/images/icon.jpg",
					icons: false
                });

                btn.onRenderMenu.add(function (c, b)
				{	
                    a.addWithPopup( b, "Buttons", "button" );
                    a.addWithPopup( b, "Alerts", "alert" );
                    a.addWithPopup( b, "Tabs", "tabs" );
                    a.addWithPopup( b, "Accordion", "accordion" );
                    
                    a.addWithPopup(b, "Posts Carousel", "posts_carousel" );
                    a.addWithPopup(b, "Icon", "icon" );
                    b.addSeparator();
                    c=b.addMenu({title: "Media elements"});
                        a.addWithPopup(c, "Image slider", "slider" );
                        a.addWithPopup(c, "Video button", "button_video" );
                        a.addWithPopup(c, "Rounded image", "img_rounded" );


                    c=b.addMenu({title: "Headings"});
                        a.addWithPopup(c, "H1", "h1" );
                        a.addWithPopup(c, "H2", "h2" );
                        a.addWithPopup(c, "H3", "h3" );
                        a.addWithPopup(c, "H4", "h4" );
                        a.addWithPopup(c, "H5", "h5" );
                        a.addWithPopup(c, "H6", "h6" );

                    c=b.addMenu({title: "Misc"});
                        a.addWithPopup(c, "Colored text", "colored_text" );
                        a.addWithPopup(c, "Lead Paragraph", "lead_paragraph" );
												a.addWithPopup(c, "Panel", "panel" );
												a.addWithPopup(c, "Progress bar", "progress_bar" );
												a.addWithPopup(c, "Pricing table", "pricing_table" );
                        a.addImmediate(c, "Only members content", "[kleo_only_members]Content to show for members only[/kleo_only_members]" );
                        a.addImmediate(c, "Only guests content", "[kleo_only_guests]Content to show for guests only[/kleo_only_guests]" );

                    c=b.addMenu({title: "Layouts"});
                        a.addWithPopup(c, "Row", "row" );
                        a.addWithPopup(c, "Columns", "columns" );
                        a.addWithPopup(c, "Section", "section" );

                    b.addSeparator();
                    c=b.addMenu({title: "Homepage"});
                        a.addWithPopup(c, "Call to action box", "call_to_action" );
                        a.addWithPopup(c, "Status icon", "status_icon" );

                    b.addSeparator();
                    c=b.addMenu({title: "Buddypress"});
                        a.addImmediate(c, "Top Members", "[kleo_top_members]" );
						a.addWithPopup(c, "Members Carousel", "members_carousel" );
                        a.addImmediate(c, "Recent Groups", "[kleo_recent_groups]" );
                        a.addWithPopup(c, "Search form", "search_members" );
						a.addWithPopup(c, "Register form", "register_form" );
                        a.addWithPopup(c, "Horizontal Search form", "search_members_horizontal" );
                        a.addImmediate(c, "Total members number", "[kleo_total_members]" );
                        a.addWithPopup(c, "Online members number", "members_online" );
                        a.addImmediate(c, "Members Statistics by field and value", '[kleo_member_stats field="" value=""]' );
						a.addImmediate(c, "Members List", "[kleo_members]" );
                                            
                                        
				});
                
                return btn;
			}
			
			return null;
		},
		addWithPopup: function ( ed, title, id ) {
			ed.add({
				title: title,
				onclick: function () {
					tinyMCE.activeEditor.execCommand("kleoPopup", false, {
						title: title,
						identifier: id
					})
				}
			})
		},
		addImmediate: function ( ed, title, sc) {
			ed.add({
				title: title,
				onclick: function () {
					tinyMCE.activeEditor.execCommand( "mceInsertContent", false, sc )
				}
			})
		}
	});
	
	// add kleoShortcodes plugin
	tinymce.PluginManager.add("kleoShortcodes", tinymce.plugins.kleoShortcodes);
})();