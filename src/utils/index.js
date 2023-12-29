/**
 * External dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

/**
 * Get product query requests for the Store API.
 *
 * @param {Object}                     request           A query object with the list of selected products and search term.
 * @param {number[]}                   request.selected  Currently selected products.
 * @param {string=}                    request.search    Search string.
 * @param {(Record<string, unknown>)=} request.queryArgs Query args to pass in.
 */
const getProductsRequests = ({
	selected = [],
	search = '',
	queryArgs = {},
}) => {
	const isLargeCatalog = true;
	const defaultArgs = {
		per_page: isLargeCatalog ? 100 : 0,
		catalog_visibility: 'any',
		search,
		orderby: 'title',
		order: 'asc',
	};
	const requests = [
		addQueryArgs('/wc/store/v1/products', {
			...defaultArgs,
			...queryArgs,
		}),
	];

	// If we have a large catalog, we might not get all selected products in the first page.
	if (isLargeCatalog && selected.length) {
		requests.push(
			addQueryArgs('/wc/store/v1/products', {
				catalog_visibility: 'any',
				include: selected,
				per_page: 0,
			})
		);
	}

	return requests;
};

const uniqBy = (array, iteratee) => {
	const seen = new Map();
	return array.filter((item) => {
		const key = iteratee(item);
		if (!seen.has(key)) {
			seen.set(key, item);
			return true;
		}
		return false;
	});
};

/**
 * Get a promise that resolves to a list of products from the Store API.
 *
 * @param {Object}                     request           A query object with the list of selected products and search term.
 * @param {number[]}                   request.selected  Currently selected products.
 * @param {string=}                    request.search    Search string.
 * @param {(Record<string, unknown>)=} request.queryArgs Query args to pass in.
 * @return {Promise<unknown>} Promise resolving to a Product list.
 * @throws Exception if there is an error.
 */
export const getProducts = ({ selected = [], search = '', queryArgs = {} }) => {
	const requests = getProductsRequests({ selected, search, queryArgs });

	return Promise.all(requests.map((path) => apiFetch({ path })))
		.then((data) => {
			const flatData = data.flat();
			const products = uniqBy(flatData, (item) => item.id);
			const list = products.map((product) => ({
				...product,
				parent: 0,
			}));
			return list;
		})
		.catch((e) => {
			throw e;
		});
};

/**
 * Get a promise that resolves to a product object from the Store API.
 *
 * @param {number} productId Id of the product to retrieve.
 */
export const getProduct = (productId) => {
	return apiFetch({
		path: `/wc/store/v1/products/${productId}`,
	});
};

/**
 * Get a promise that resolves to a list of attribute objects from the Store API.
 */
export const getAttributes = () => {
	return apiFetch({
		path: `wc/store/v1/products/attributes`,
	});
};

/**
 * Get a promise that resolves to a list of attribute term objects from the Store API.
 *
 * @param {number} attribute Id of the attribute to retrieve terms for.
 */
export const getTerms = (attribute) => {
	return apiFetch({
		path: `wc/store/v1/products/attributes/${attribute}/terms`,
	});
};
