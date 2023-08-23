/**
 * External dependencies
 */
import { getBlockType } from '@wordpress/blocks';

const POSITION_CLASSNAMES = {
	'top left': 'is-position-top-left',
	'top center': 'is-position-top-center',
	'top right': 'is-position-top-right',
	'center left': 'is-position-center-left',
	'center center': 'is-position-center-center',
	center: 'is-position-center-center',
	'center right': 'is-position-center-right',
	'bottom left': 'is-position-bottom-left',
	'bottom center': 'is-position-bottom-center',
	'bottom right': 'is-position-bottom-right',
};

/**
 * Check if content position center.
 *
 * @param {string} contentPosition Content Position.
 * @return {boolean} if content position center
 */
export function isContentPositionCenter(contentPosition) {
	return (
		!contentPosition ||
		contentPosition === 'center center' ||
		contentPosition === 'center'
	);
}

/**
 * Get content position center.
 *
 * @param {string} contentPosition Content Position.
 * @return {string} content position
 */
export function getPositionClassName(contentPosition) {
	/*
	 * Only render a className if the contentPosition is not center (the default).
	 */
	if (isContentPositionCenter(contentPosition)) {
		return '';
	}

	return POSITION_CLASSNAMES[contentPosition];
}

/**
 * Last specific fixed number after dot
 *
 * @param {string|number} number Given number.
 * @param {number}        limit  position limit.
 * @return {number} content position
 */

export function getToFixed(number, limit = 1) {
	return Number.parseFloat(Number.parseFloat(number).toFixed(limit));
}

export function calculateCursorPosition(event, element) {
	const { left, top, width, height } = element.getBoundingClientRect();

	const x = getToFixed(((event.x - left) / width) * 100);
	const y = getToFixed(((event.y - top) / height) * 100);

	return { x, y };
}

export function getSelectedPointer(event, parentElement) {
	const pointers = parentElement.querySelectorAll('.pointer');

	for (const pointer of pointers) {
		if (pointer.contains(event.target)) {
			return pointer;
		}
	}
}

export function delay(
	callbackFunction,
	waitTime,
	...callbackFunctionArguments
) {
	setTimeout(() => callbackFunction(...callbackFunctionArguments), waitTime);
}

/**
 * PHP Style Uppercase Word
 *
 * @param {string} string word
 * @return {string} changed word
 */

export function ucWords(string) {
	return string
		.toString()
		.replace(/[-_]/g, ' ')
		.replace(/\b[a-z]/g, (str) => str.toUpperCase());
}

export function flexAlignment(align) {
	if (align === 'top') {
		return 'flex-start';
	}

	if (align === 'bottom') {
		return 'flex-end';
	}

	return align;
}

export function isBlockRegistered(blocks = []) {
	return blocks.some((block) => {
		return typeof getBlockType(block) !== 'undefined';
	});
}
