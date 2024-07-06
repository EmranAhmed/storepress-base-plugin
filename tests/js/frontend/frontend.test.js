/**
 * External dependencies
 */
import {describe, expect, it} from '@jest/globals'
import { getOptionsFromAttribute } from '@storepress/utils';
import { render, fireEvent, screen } from '@testing-library/react';

// @example: https://developer.wordpress.org/block-editor/contributors/code/testing-overview/

describe( 'frontend scripts', () => {

	it( 'should return sub attribute value', () => {

		document.body.innerHTML = `<div id="element" data-slider-settings--url="example.com"></div>`;

		const element = document.querySelector( '#element' );

		const result = getOptionsFromAttribute(element, 'slider-settings')

		expect( result.url ).toEqual('example.com');
	})

	it( 'should return sub attribute value also have full attribute object defined', () => {

		document.body.innerHTML = `<div id="element" data-slider-settings='{"hello":"world"}' data-slider-settings--hello="w"></div>`;

		const element = document.querySelector( '#element' );

		const result = getOptionsFromAttribute(element, 'slider-settings')

		expect( result.hello ).toEqual('w');
	})

	it( 'should return non attribute object value', () => {

		document.body.innerHTML = `<div id="element" data-slider-settings='{"hello":"world"}' data-slider-settings--hello="w"></div>`;

		const element = document.querySelector( '#element' );

		const result = getOptionsFromAttribute(element, 'slider-settings')

		expect( result.hello ).not.toEqual('world');
	})
} );
