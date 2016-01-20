/**
 * 
 */
;(function(){
	
	tinymce.PluginManager.add('cf_featured_post', function( editor, url ){
		/**
		 * Put a visual representation on shortcodes
		 */
		function replaceShortcodes( content ) {
			return content.replace( /\[codeflavors_featured_post([^\]]*)\]/g, function( match ) {
				var r = html( 'cffp', match );
				return r;
			});
		}
		
		function html( cls, data ){
			data = window.encodeURIComponent( data );
			return '<img src="' + tinymce.Env.transparentSrc + '" class="codeflavors_fp mceItem ' + cls + '" ' + 
			'data-cffp="' + data + '" data-mce-resize="false" />';			
		}
		
		/**
		 * Restore the shortcodes
		 */
		function restoreShortcodes( content ) {
			function getAttr( str, name ) {
				name = new RegExp( name + '=\"([^\"]+)\"' ).exec( str );
				return name ? window.decodeURIComponent( name[1] ) : '';
			}

			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
				var data = getAttr( image, 'data-cffp' );

				if ( data ) {
					return '<p>' + data + '</p>';
				}

				return match;
			});
		}
		
		var select = function(){
			/*
			editor.windowManager.open({
				title 		: editor.getLang('fa_slider.select_win_title'),
				autoScroll 	: true,
				height		: 900,
				width 		: 900,
				html 		: '',
				buttons		: [
				     { text : editor.getLang('fa_slider.close_win'), onclick: 'close' }
				]
			});
			*/
			edit( false, editor.getLang('cffp.add_new_window_title') );
		}	
		
		var edit = function( node, title ){
			
			var data = {};
			
			if( node ){
				var code = window.decodeURIComponent( editor.dom.getAttrib( node, 'data-cffp' ) );
				code.replace( /([a-z\_?]+)\="([^\"]+)/ig, function( a, b, c, d, e ){					
					data['cffp_' + b] = c;
				});
			}	
			
			console.log(data);
			
			// Advanced dialog shows general+advanced tabs
			win = editor.windowManager.open({
				title: title || editor.getLang('cffp.window_title'),
				data: data,
				body: [
					{
						title: 'Advanced',
						type: 'form',
						pack: 'start',
						items: [
							{
								label: editor.getLang('cffp.label_post_type'),
								name : 'cffp_post_type',
								type : 'listbox',
								values: cffp_post_types
							},
							{
								label: editor.getLang('cffp.label_taxonomy'),
								name : 'cffp_taxonomy',
								type : 'listbox',
								values : cffp_taxonomies
							},
							{
								label: editor.getLang('cffp.label_category'),
								name : 'cffp_category',
								type : 'textbox'
							},
							{
								label : editor.getLang('cffp.label_post_num'),
								name  : 'cffp_post_num',
								type  : 'textbox',
								value : '1'
							},
							{
								label : editor.getLang('cffp.label_post_offset'),
								name  : 'cffp_offset',
								type  : 'textbox',
								value : '0'
							},
							{
								label: editor.getLang('cffp.label_post_id'),
								name : 'cffp_post_id',
								type : 'textbox'
							},
							{
								label: editor.getLang('cffp.label_template'),
								name : 'cffp_template',
								type : 'listbox',
								values : cffp_templates
							},
						]
					}
				],
				onSubmit: function(e){
					var s = '[codeflavors_featured_post post_type="' + e.data.cffp_post_type + '" category="' + e.data.cffp_category + '" taxonomy="' + e.data.cffp_taxonomy + '" post_num="' + e.data.cffp_post_num + '" offset="' + e.data.cffp_offset +'" post_id="' + e.data.cffp_post_id + '" template="' + e.data.cffp_template + '"]';					
					if( node ){ 
						editor.dom.setAttrib( node, 'data-cffp', window.encodeURIComponent( s ) );
					}
					editor.insertContent( s );
				}
			});
			
		}
		
		editor.on( 'mouseup', function( event ) {
			var dom 	= editor.dom,
				node 	= event.target;
			
			function unselect() {
				dom.removeClass( dom.select( 'img.wp-cfp-selected' ), 'wp-cfp-selected' );
			}

			if ( node.nodeName === 'IMG' && dom.getAttrib( node, 'data-cffp' ) ) {
				// Don't trigger on right-click
				if ( event.button !== 2 ) {
					if ( dom.hasClass( node, 'wp-cfp-selected' ) ) {
						edit( node );
					} else {
						unselect();
						dom.addClass( node, 'wp-cfp-selected' );
					}
				}
			} else {
				unselect();
			}
		});
		
		editor.on( 'BeforeSetContent', function( event ) {
			event.content = replaceShortcodes( event.content );			
		});
		
		editor.on( 'PostProcess', function( event ) {
			if ( event.get ) {
				event.content = restoreShortcodes( event.content );
			}
		});
		
		// Register button
		editor.addButton( 'cf_featured_post', {
			title 	: editor.getLang('cffp.button_title'),
			onclick : select,
			image 	: url + '/ico.png'
		});
	});	
})();