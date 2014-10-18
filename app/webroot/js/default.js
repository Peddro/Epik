var ids, classes;

/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	body = $('body');
	
	ids = E.selectors.ids;
	classes = E.selectors.classes;
	
	// Browsers - Adds a class to body that specifies the browser type.
	if($.browser.chrome) {
		body.addClass('chrome');
	}
	else if($.browser.safari) {
		body.addClass('safari');
	}
	else if($.browser.opera) {
		body.addClass('opera');
	}
	else if($.browser.msie) {
		body.addClass('msie');
	}
	else if($.browser.mozilla) {
		body.addClass('moz');
	}
	
	
	/**
	 * Utilities functions used on all Pages
	 *
	 * @package E.utils
	 * @author Bruno Sampaio
	 */
	E.utils = {

		/**
		 * Set content element height to fit the entire browser window including the header/toolbar and the footer.
		 *
		 * @param int windowHeight - the window height.
		 */
		makePageFitWindow : function(windowHeight) {
			var content = $('#'+ids.content);

			// Set content height
			var headerHeight = ($('#'+ids.header).length > 0)? $('#'+ids.header).outerHeight() : $('#toolbar').outerHeight();
			var contentHeight = parseInt($('#'+ids.content).css('padding-top'))-parseInt($('#'+ids.content).css('padding-bottom'));
			var footerHeight = $('#'+ids.footer).outerHeight();
			content.height(windowHeight-headerHeight-contentHeight-footerHeight);

			// Set navigation height
			var navigation = $('#'+ids.navigation);
			if(navigation.length > 0) {
				var navigationHeight = windowHeight-headerHeight;
				var rightHeight = content.outerHeight() + footerHeight;
				if(rightHeight > navigationHeight) {
					navigationHeight = rightHeight;
				}
				navigation.height(navigationHeight);
			}
		},
		
		
		/**
		 * Set TipTip pop-ups to all 'items' with a certain max 'width' and 'position'.
		 *
		 * @param DOM items - the items with a 'title' attribute.
		 * @param int width - the pop-ups max width.
		 * @param string position - the pop-ups position in relation to the items.
		 */
		setTipTips : function(items, width, position) {
			items.tipTip({maxWidth: width, defaultPosition: position});
		},
		
		
		/**
		 * Set all links inside 'container' without modal class as external links.
		 *
		 * @param DOM container - the links container.
		 */
		setTargetLinks : function(container) {
			container.find('a:not(.'+classes.modal+')').attr('target', '_blank');
		},
		
		
		/**
		 * Associates events to the elements inside a 'file-info' element.
		 *
		 * @param DOM container - the parent container.
		 */
		setFilePreviewEvents : function(container) {
			container.find('.file-info .remove .icon-small').click(function(event) {
				var container = $(this).parents('.file-info');

				container.children('input[type=hidden]').attr('value', null);
				container.children('.preview').fadeOut('fast', function() {
					$(this).remove();
				});
				$(this).parent().remove();
			});
		},
		
		
		/**
		 * Get Selected Text Size on Page
		 */
		getSelectedTextSize : function() {
			var text = '';
			if(window.getSelection) {
				text = window.getSelection().focusOffset;
		  	} 
			else if(document.getSelection) { 
				text = document.getSelection().focusOffset;
			} 
			else if(document.selection) { 
				text = document.selection.createRange().text.length;
			}
			
			return text;
		},
		
		
		/**
		 * Expands/Collapses a element.
		 *
		 * @param string items - the selector string for the items to trigger the expand or collapse event.
		 */
		setExpandCollapse : function(items) {
			var slideTime = 400;
			$(items).click(function(event) {
				var self = $(this);
				var list = self.parent().parent().next('.'+classes.list);
				
				if(list.length > 0) {
					if(list.is(':visible')) {
						list.animate({ height: 0 }, slideTime, function() {
							list.hide();
							self.removeClass('minimize').addClass('maximize');
						});
					}
					else {
						var height = list.css({ height: '', position: 'absolute', visibility: 'hidden' }).height();
						list.css({ height: 0, position: '', visibility: '' }).show();
						list.animate({'height' : height}, slideTime, function() {
							list.css('height', '');
							self.removeClass('maximize').addClass('minimize');
						});
					}
				}
			});
		},
		
		
		/**
		 * Clones an object
		 *
		 * @param Object object - object to be cloned.
		 * @return object - cloned object;
		 */
		cloneObject : function(object) {
			return $.extend(true, {}, object);
		}

	};
	
	
	/**
	 * Ajax functions
	 *
	 * @package E.ajax
	 * @author Bruno Sampaio
	 */
	E.ajax = {

		/**
		 * Hides all elements inside 'dom' and shows the ajax icon when a form submission starts.
		 *
		 * @param DOM dom - the container of elements to hide and to apply the ajax class.
		 * @param string hide - the selector for the elements to hide.
		 */
		start : function(dom, hide) {
			if(!hide) hide = '';
			$(dom).addClass(classes.ajax);
			return $(dom).children(hide).fadeOut('fast');
		},
		
		
		/**
		 * Shows all elements inside 'dom' and hides the ajax icon when a form submission is finished.
		 *
		 * @param DOM dom - the container of elements to show and to remove the ajax class.
		 * @param string show - the selector for the elements to show.
		 */
		finish : function(dom, show) {
			if(!show) show = '';
			$(dom).removeClass(classes.ajax);
			
			E.modal.apply($(dom).find('.modal'));
			E.utils.setTipTips($(dom).find('*[title]'), '100px', 'bottom');
			
			return $(dom).children(show).fadeIn('fast');
		},
		
		
		/**
		 * Submit and Ajax Request
		 *
		 * @param string method - the HTTP request method to be used.
		 * @param string url - the url to request.
		 * @param string data - the data to send.
		 * @param string dataType - the response format.
		 * @param bool processData
		 * @param bool contentType
		 * @param DOM container - the container for the data.
		 * @param function success - callback function to be called on success.
		 * @param function complete - callback function to be called on complete.
		 */
		request: function(method, url, data, dataType, processData, contentType, success, complete) {
			contentType = contentType? 'application/x-www-form-urlencoded; charset=UTF-8' : contentType;

			$.ajax({
				url: url,
				type: method,
				data: data,
				dataType: dataType,
				processData: processData,
				contentType: contentType,
				error: function(jqXHR, textStatus, errorThrown) {
					console.error(errorThrown);
					E.ajax.error(errorThrown, jqXHR.responseText);
				},
				success: function (data, textStatus) {
					success(data);
				},
				complete: function() {
					complete();
				}
			});
		},
		
		
		/**
		 * Handles a Ajax Request Error.
		 *
		 * @param DOM error - the error type.
		 */
		error : function(error, content) {
			var modal = E.modal.getContent();
			var url = E.system.server;

			switch(error) {
				case 'Forbidden':
					url+= 'users/signin';
					break;
				
				default:
					url = false;
					break;
			}
			
			if(url) {
				this.start(modal);
				this.request('get', url, '', 'html', true, true, 
					function(data) {
						E.modal.open(modal, data);
					}, 
					function() {
						E.ajax.finish(modal);
					}
				);
			}
			else E.modal.open(modal, content);
		},
		
		
		/**
		 * Binds all necessary events to all ajax forms inside 'elements'.
		 *
		 * @param elements - section elements.
		 * @param args - arguments to be passed to the form submission function.
		 */
		generalEvents : function(elements, args) {
			var list = elements.children('.'+classes.list);

			if($.isEmptyObject(args)) {
				args = {
					container : list,
					action : 'replace',
					remove : ''
				};
			}

			var form = elements.find('.'+classes.header+' form');
			this.filter(form);
			this.search(form);
			this.optionsSubmit(form, args);
			this.paginate(list.children('.'+classes.paging));
		},
		
		
		/**
		 * Binds change event to form select fields.
		 *
		 * @param elements - form elements.
		 */
		filter : function(elements) {
			elements.find('select').change(function(event) {
				var value = $(this).val();
				$(this).find('option').removeAttr('selected');
				$(this).find('option[value='+value+']').attr('selected', true);
				$(this).closest('form').submit();
			});
		},
		
		
		/**
		 * Binds key typing and form submit events to search fields contained inside 'elements'.
		 * The root element of 'elements' must be a form and it may be inside a '.section' element, which also contains a '.list' element and, possibly, a '.paging' element.
		 *
		 * @param elements - form elements.
		 */
		search : function(elements) {
			var timer = null;

			$(elements.find('input[type=search]')).keyup(function(){
				var form = $(this).closest('form');
			    timer = setTimeout(function() {
			    	form.submit();
			    }, 500);
			});

			//on keydown, clear the countdown 
			$(elements.find('input[type=search]')).keydown(function(){
			    clearTimeout(timer);
			});
		},
		
		
		/**
		 * Binds click event to all links inside 'elements' which will be used to get next page from a list of items.
		 * The root element of 'elements' must be a '.paging' and it must be inside a '.section' element, which also contains a '.list' element.
		 *
		 * @param elements - elements with class '.paging'.
		 */
		paginate : function(elements) {
			elements.each(function(index) {
				var list = $(this).parent('.'+classes.list);
				list.after($(this).detach());
			});

			elements.click(function(event) {
				event.preventDefault();

				var box = $(this);
				var list = box.prev('.'+classes.list);
				var link = box.children('a');
				link.hide();

				E.ajax.start(box);
				E.ajax.request('get', link.attr('href'), '', 'html', true, true, 
					function(data) {
						data = $(data);
						E.modal.apply(data.find('.'+classes.modal));
						list.append(data);

						E.utils.setTipTips(data.find('*[title]'), '100px', 'bottom');
						E.ajax.paginate(list.children('.'+classes.paging));
					}, 
					function() {
						box.fadeOut('fast');
					}
				);
			});
		},
		
		
		/**
		 * Submits a ajax form inside '.options' div.
		 *
		 * @param list elements - the form elements.
		 * @param object args - arguments which define the actions to realize:
		 *				- args.container: container where elements must be added and removed;
		 *				- args.remove: the elements to be removed;
		 * 				- args.action: the action to execute when data is received (replace, prepend or append);
		 */
		optionsSubmit : function(elements, args) {
			elements.submit(function(event) {
				event.preventDefault();

				var form = $(this).clone(true, true);
				var container = args.container;
				var section = $(this).closest('.'+classes.section);
				var list = section.children('.'+classes.list);
				var pagingLink = section.children('.'+classes.paging);

				$('#flashMessage').remove();

				var contents = E.ajax.start(container, args.remove);
				if(args.remove.length > 0) {
					contents.remove();
				}

				if(pagingLink.length > 0) {
					pagingLink.remove();
				}

				E.ajax.request('post', form.attr('action'), form.serialize(), 'html', true, true, 
					function(data) {
						data = $(data);

						switch(args.action) {
							case 'replace':
								container.html(data);
								break;

							case 'prepend':
								container.prepend(data);
								break;

							case 'append':
								container.append(data);
								break;
						}
						
						E.ajax.paginate(list.children('.'+classes.paging));
					}, 
					function() {
						E.ajax.finish(container, args.remove);
						$(window).resize();
					}
				);
			});
		},
		
		
		/**
		 * Sets a form submission event with files on it.
		 *
		 * @param DOM form - the form element.
		 */
		filesSubmit : function(form) {
			var input = form.find('input[type=file]'), formdata = false;  
			if(input.length > 0) {

				// Change Source
				form.find('#FileSource').change(function(event) {
					var upload = form.find('div.upload');
					var external = form.find('div.external');

					if($(this).attr('value') == 1) {
						upload.removeClass(classes.selected);
						external.addClass(classes.selected);
					}
					else {
						external.removeClass(classes.selected);
						upload.addClass(classes.selected);
					}
				});

				// Submit Form
				form.submit(function(event) {
					event.preventDefault();

					if (window.FormData) {  
				        formdata = new FormData();

						var inputs = form.find('input[type=text], input[type=hidden], select, textarea');
						inputs.each(function(index, value) {
							formdata.append($(value).attr('name'), $(value).attr('value'));
						});

						if(input[0].files.length > 0) {
							formdata.append(input.attr('name'), input[0].files[0]);
						}

						var container = E.modal.getContent();
						var list = container.find('.'+classes.list);

						E.ajax.start(list);
						E.ajax.request('post', form.attr('action'), formdata, 'html', false, false,
							function(data) {
								container.html(data);
							}, 
							function() {
								E.ajax.finish(list);
							}
						);
				    }
				});
			}
		}
	};
	
	
	/**
	 * Modal Window functions
	 *
	 * @package E.modal
	 * @author Bruno Sampaio
	 */
	E.modal = {
		
		/**
		 * Opens a Modal Window
		 *
		 * If the window is not yet open creates an empty div with class modal, 
		 * binds the modal window event to it and then triggers the click event on it.
		 * After that the window content is set.
		 *
		 * @param DOM modal - the modal window DOM.
		 * @param string content - the content to be displayed.
		 */
		open : function(modal, content) {

			// Create element to open modal window if it isn't already opened
			if(!modal.is(':visible')) {
				var link = $('<div class="modal" />');
				E.modal.apply(link);
				link.click();
			}

			// Set modal window HTML content
			modal.html(content);
		},
		
		
		/**
		 * Get Modal Window
		 * 
		 * Selects and returns the modal window with id #modal inside #boxes.
		 */
		getWindow : function() {
			return $('#boxes > #'+ids.modal);
		},
		
		
		/**
		 * Get Modal Window Mask
		 * 
		 * Selects and returns the modal window mask with id #mask inside #boxes.
		 */
		getMask : function() {
			return $('#boxes > #'+ids.mask);
		},
		
		
		/**
		 * Get Modal Window Content
		 * 
		 * Selects and returns the modal window content with id #modal_content inside #modal.
		 */
		getContent : function(dom) {
			var selector = '#'+ids.modal+'_'+ids.content;
			return dom? dom.children(selector) : this.getWindow().children(selector);
		},
		
		
		/**
		 * Binds click event to open a Modal Window to all 'elements'.
		 * The root element of 'elements' must be a link with class '.modal'.
		 *
		 * @param list elements - links with class '.modal'.
		 */
		apply : function(elements) {
			var self = this;
			
			elements.click(function(event) {
				if(!$(this).hasClass('disabled')) {
					event.preventDefault(); //Cancel the link behavior

					//Get modal window
					var modal = self.getWindow();

					if(!modal.is(':visible')) {
						var mask = self.getMask();

						//Get the window width and height
						var winW = $(window).width();
						var winH = $(window).height();

						self.setStyles(mask, modal, winW, winH);

						//transition effect		
						mask.fadeIn(1000);
						mask.fadeTo("slow",0.8);

						//transition effect
						modal.fadeIn(2000, function() {
							self.resize();
						});
					}

					// Get Url for Request
					var url = false;
					if($(this).get(0).tagName == 'A') {
						url = $(this).attr('href');
					}
					else if($(this).children('a').length > 0) {
						url = $(this).children('a').attr('href');
					}

					// Set Request Events
					if(url) {
						var container = self.getContent(modal);
						E.ajax.start(container);

						E.ajax.request('get', url, '', 'html', true, true,
							function(data) {
								container.html(data);
							}, 
							function() {
								E.ajax.finish(container);
							}
						);
					}
				}
			});
		},
		
		
		/**
		 * Calculate 'mask' and 'modal' window dimensions and positions.
		 * 
		 * @param mask - the element mask.
		 * @param modal - the modal window.
		 * @param containerWidth - the window container width (usually it's the browser window width).
		 * @param containerHeight - the window container height (usually it's the browser window height).
		 */
		setStyles : function(mask, modal, containerWidth, containerHeight) {
			
			//Set heigth and width to mask to fill up the whole screen
			mask.css({'width':containerWidth, 'height':containerHeight, 'top' : $(document).scrollTop()});

			//Set the popup window to center
		    modal.css('top',  containerHeight/2 - modal.outerHeight()/2);
		    modal.css('left', containerWidth/2 - modal.outerWidth()/2);
		},
		
		
		/**
		 * Resize modal window contents.
		 */
		resize : function() {
			var section = this.getContent().find('.'+classes.section);
			if(section.length > 0) {
				var list = section.children('.'+classes.list);
				list.outerHeight(section.height() - (section.children('.'+classes.header).height() + parseInt(list.css('margin-top')) + parseInt(list.css('margin-bottom'))));
			}
		},
		
		
		/**
		 * Sets a modal window page events.
		 * Called each time a request is made inside a modal window.
		 */
		generalEvents : function() {
			var self = this;
			var page = $('body').attr('id');
			var section = self.getContent().find('.'+classes.section);
			var list = section.children('.'+classes.list);

			// Click on previous
			var form = section.children('.'+classes.list).find('form');
			form.find('button[name=previous]').click(function(event) {
				var previous = form.children('#form_previous');
				form.children('#form_current').attr('value', previous.attr('value'));

				form.find('input[type=submit]').click();
			});

			// Click on Redirect
			form.find('button[name=redirect]').click(function(event) {
				var input = form.find('.item.selected input');
				if(input.length > 0) {
					var url = form.attr('action') + '/' + input.val();
					window.open(url, '_blank');
					window.focus();
					self.close();
				}
			});

			// Click on Insert
			form.find('button[name=insert]').click(function(event) {
				var input = form.find('.item.selected input');
				if(input.length > 0) {
					E.tools.insertFromChosen(parseInt(input.val()));
					self.close();
				}
			});

			// Click on Cancel
			form.find('button[name=cancel]').click(function(event) {
				self.close();
			});

			// Pages specific
			if(page == 'projects-view') {
				E.utils.setTargetLinks(section);
			}

			// Sections specific
			if(section.hasClass('select')) {
				self.setSelectEvents();
			}
			else if(section.hasClass('choose')) {
				E.ajax.generalEvents(section, {container : form, action : 'prepend', remove: '.chooser'});
			}
			else if(section.hasClass('file')) {
				E.utils.setFilePreviewEvents(section);
				E.ajax.filesSubmit(form);
			}
			
			self.resize();
		},
		
		
		/**
		 * Events for a Select Page
		 * 
		 * By clicking an item the link inside it is also clicked.
		 */
		setSelectEvents : function() {
			var items = this.getWindow().find('.'+classes.list+' .'+classes.item);
			items.click(function(event) {
				$(this).find('a').click();
			});
		},
		
		
		/**
		 * Events for a Choose Page
		 * 
		 * By clicking an item:
		 *	- If it is a radio it is selected by deselecting the others;
		 *	- If it is a checkbox it becomes selected whitout changing the others.
		 */
		setChooserEvents : function() {
			var self = this;
			var container = this.getWindow().find('.'+classes.list+' .chooser');
			var items = container.find('.'+classes.item);

			// Set TipTip's on Items
			E.utils.setTipTips(items.filter('[title]'), '100px', 'bottom');

			// Set Click Event on Items
			items.click(function(event) {
				var input = $(this).children('input[type=checkbox]');

				if(input.length > 0) {
					if($(this).hasClass(classes.selected)) {
						$(this).removeClass(classes.selected);
						$(this).removeClass('yellow');
						input.attr('checked', false);
					}
					else {
						$(this).addClass(classes.selected);
						$(this).addClass('yellow');
						input.attr('checked', true);
					}
				}
				else if((input = $(this).children('input[type=radio]')).length > 0) {
					var selected = items.filter('.'+classes.selected);
					selected.removeClass(classes.selected);
					selected.removeClass('yellow');
					selected.children('input[type=radio]').attr('checked', false);

					$(this).addClass(classes.selected);
					$(this).addClass('yellow');
					input.attr('checked', true);
				}
			});
			
			// Set Mansonry on Items
			setTimeout(function() {
				container.masonry({ itemSelector : '.'+classes.item, columnWidth : 116, isAnimated : false, isResizable : false });
				self.resize();
			}, 200);
		},
		
		
		/**
		 * Sets the Modal Window Close Event
		 */
		setCloseEvent : function() {
			var self = this;
			$('#'+ids.mask+', .window .close').click(function (event) {
				self.getMask().fadeTo('fast', 1).hide();
				self.getContent(self.getWindow().hide()).html('');

				if($('body').hasClass('dashboard')) {
					$('#'+ids.content+' .'+classes.header+' .options form').submit();
				}
			});
		},
		
		
		/**
		 * Close Modal Window
		 */
		close : function() {
			this.getWindow().children('.close').click();
		}
	};
	
	
	// Pages Properties
	var page = body.attr('id'), notProject = page != 'projects-view';
	
	// Dashboard
	if(body.hasClass('dashboard')) {

		// Events and Ajax Requests - Bind all events and ajax requests.
		E.ajax.generalEvents($('.'+classes.section), {});
		
		// TipTip Events
		E.utils.setTipTips($('#'+ids.navigation).children(), '350px', 'right');
		
		// Helper Events
		var helper = $('#helper');
		helper.children('.picture').click(function() {
			var self = $(this), item = helper.children('.'+classes.item);
			
			if(helper.hasClass(classes.selected)) {
				helper.animate({ right : -item.outerWidth() }, 300).removeClass(classes.selected);
			}
			else {
				helper.animate({ right : 0 }, 300).addClass(classes.selected);
			}
			
		});
	}
	else if(page.startsWith('sessions')) {
		E.utils.setExpandCollapse('.minimize');
	}
	
	// TipTip Events for General Pages
	if(notProject) {
		E.utils.setTipTips($('#'+ids.content).find('.options *[title]'), '300px', 'bottom');
		$('input[title], select[title], textarea[title]').tipTip({maxWidth: '300px', activation: 'focus', defaultPosition: 'right'});
	}
	
	// Files Preview
	E.utils.setFilePreviewEvents(body);
	
	// Modal Window - Binds links click event and close button event.
	E.modal.apply($('.'+classes.modal));
	E.modal.setCloseEvent();
	
	// Finally resize window.
	if(notProject) {
		$(window).resize();
	}
	
});


/**
 * Triggered on page scroll.
 */
$(document).scroll(function(event) {
	
	// Modal Window - Maintain mask always in background filling the entire screen.
	if(typeof ids != 'undefined') {
		var mask = E.modal.getMask();
		if(mask.is(':visible')) {
			mask.css({ top : $(this).scrollTop() });
		}
	}

});


/**
 * Triggered on window resize.
 */
$(window).resize(function() {
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	
	// Page Specific Properties
	var body = $('body');
	var page = body.attr('id');
	var content = $('#'+ids.content);
	
	if(!body.hasClass('overflow')) {
		
		// Set Main Page Elements Size
		E.utils.makePageFitWindow(windowHeight);
		
		var sections = content.find('.'+classes.section);
		var message = content.children('#flashMessage');
		
		if(message.length > 0) {
			var container = (sections.parent().attr('id') == ids.content)? sections : sections.parent();
			var currentHeight = container.height();
			container.height(currentHeight - message.outerHeight() - parseInt(message.css('margin-top')) - parseInt(message.css('margin-bottom')));
		}
		
		// Dashboard
		if(body.hasClass('dashboard')) {
			
			if(sections.length > 0) {
				sections.each(function(index) {
					var section = $(this);
					
					var header = section.children('.'+classes.header);
					var list = section.children('.'+classes.list);
					var paging = section.children('.'+classes.paging);
					
					var listHeight = section.height() - header.height() - parseInt(list.css('margin-top')) - parseInt(list.css('margin-bottom'));
					if(paging.length > 0) {
						listHeight-= paging.height();
					}
					list.height(listHeight);
				});
			}
		}
		
		// Projects
		else if(page == 'projects-view') {
			
			// Toolbar
			if(windowWidth < 1270) {
				$('#'+ids.toolbar + ', ' + '#'+ids.menus).addClass('small').find('.icon').removeClass('icon').addClass('icon-small');
			}
			else {
				if($('#'+ids.toolbar).hasClass('small')) {
					$('#'+ids.toolbar + ', ' + '#'+ids.menus).removeClass('small').find('.icon-small').removeClass('icon-small').addClass('icon');
				}
			}

			// Menus
			if(typeof currentProject != 'undefined') {
				E.menus.setToolbarMenuPosition(currentProject.tool, currentProject.menu, windowWidth);
			}

			// Main Sections
			if(sections.length > 0) {
				sections.each(function(index) {
					var section = $(this);
					var header = section.children('.'+classes.header);
					var list = section.children('.'+classes.list);

					list.outerHeight(section.height() - header.outerHeight());

					if(section.hasClass('collapsed')) {
						header.outerWidth(section.height());
					}
				});
				
				E.game.utils.setScreenTopMargin();
			}
		}
		
	}
	
	// Static Pages
	else {
		var contentHeight = windowHeight - ($('#'+ids.header).outerHeight() + $('#'+ids.footer).outerHeight());
		content.css('min-height', contentHeight);
	}
	
	
	// Modal Window
	var mask = E.modal.getMask();
	var modal = E.modal.getWindow();
	
	if(mask.is(':visible') && modal.is(':visible')) {
		E.modal.setStyles(mask, modal, windowWidth, windowHeight);
	}
});
