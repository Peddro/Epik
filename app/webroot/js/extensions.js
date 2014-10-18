$(document).ready(function() {
	
	/**
	 * Extensions to Javascript Core Objects
	 */
	
	// String startsWith
	if (typeof String.prototype.startsWith !== 'function') {
		String.prototype.startsWith = function (str) {
			return this.slice(0, str.length) == str;
		};
	}
	
	// String endsWith
	if (typeof String.prototype.endsWith !== 'function') {
		String.prototype.endsWith = function (str) {
			return this.slice(this.length - str.length, this.length) == str;
		};
	}
	
	// Strings Uppercase First
	if (typeof String.prototype.ucfirst !== 'function') {
		String.prototype.ucfirst = function() {
		    return this.charAt(0).toUpperCase() + this.slice(1);
		}
	}
	
	// Array Move
	if (typeof Array.prototype.move !== 'function') {
		Array.prototype.move = function (old_index, new_index) {
		    if (new_index >= this.length) {
		        var k = new_index - this.length;
		        while ((k--) + 1) {
		            this.push(undefined);
		        }
		    }
		    this.splice(new_index, 0, this.splice(old_index, 1)[0]);
		};
	}
	
	
	/**
	 * Extensions to jQuery Objects
	 */
	
	// Enable Element
	jQuery.fn.enable = function() {
		return $(this).removeClass(classes.disabled);
	};
	
	// Disable Element
	jQuery.fn.disable = function() {
		return $(this).addClass(classes.disabled);
	};
	
	// Check if element is disabled
	jQuery.fn.isDisabled = function() {
		return $(this).hasClass(classes.disabled);
	};
	
});