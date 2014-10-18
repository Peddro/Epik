/**
 * Utilities functions
 *
 * @package E.utils
 * @author Bruno Sampaio
 */

/**
 * Round a Score Value for Presentation
 *
 * @param double num - the score value.
 * @return double
 */
exports.roundScore = function(num) {
	return Math.round(num*10)/10;
}

/**
 * Get Random Number from 0 to 'limit'
 *
 * @param int limit - the range limit.
 * @return int
 */
exports.random = function(limit) {
	return Math.round(Math.random() * limit);
}