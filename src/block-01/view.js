/**
 * External dependencies
 */
import domReady from '@wordpress/dom-ready';
import { getProductsRequests } from '../utils';

domReady(function () {
	const points = document.querySelectorAll(
		'.wp-block-storepress-hotspot-popup > .pointer__item'
	);

	document.addEventListener('keydown', function (event) {
		const isEscape = event.key === 'Escape' || event.key === 'Esc';

		if (isEscape) {
			points.forEach((point) => {
				point.classList.remove('active');
			});
		}
	});

	document.addEventListener('pointerdown', function (event) {
		const wrapper = event.target.closest('.hotspot-tooltip');

		if (!wrapper) {
			points.forEach((point) => {
				point.classList.remove('active');
			});
		}
	});

	points.forEach((point) => {
		point.addEventListener('pointerdown', function (event) {
			event.stopPropagation();

			const current = this;

			points.forEach((item, i) => {
				if (points[i] !== current) {
					item.classList.remove('active');
				}
			});

			this.classList.toggle('active');
		});
	});
});
