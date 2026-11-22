/**
 * Global Vitest setup. Runs before each test file.
 */

/**
 * External dependencies
 */
import '@testing-library/jest-dom/vitest';
import { afterEach } from 'vitest';
import { cleanup } from '@testing-library/react';

afterEach( cleanup );
