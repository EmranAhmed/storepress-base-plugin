/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	Spinner,
	BaseControl
} from '@wordpress/components';
import {
	useState,
	useRef,
	useLayoutEffect
} from '@wordpress/element';
import { Icon, closeSmall, search } from '@wordpress/icons';
import classnames from 'classnames';
import { useInstanceId } from '@wordpress/compose';
import { get } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';

function SearchControl({
						   className,
						   onChange,
						   onKeyDown,
						   value,
						   label,
						   isLoading = false,
						   placeholder = __('Search'),
						   hideLabelFromVision = true,
						   help,
						   onClose,
						   ...restProps
					   }) {
	const searchRef  = useRef();
	const instanceId = useInstanceId(SearchControl);
	const id         = `storepress-components-search-control-${instanceId}`;

	const renderRightButton = () => {

		if (isLoading) {
			return (
				<Spinner/>
			);
		}

		if (onClose) {
			return (
				<Button
					icon={closeSmall}
					label={__('Close search')}
					onClick={onClose}
				/>
			);
		}

		if (!!value) {
			return (
				<Button
					icon={closeSmall}
					label={__('Reset search')}
					onClick={() => {
						onChange('');
						searchRef.current?.focus();
					}}
				/>
			);
		}

		return <Icon icon={search}/>;
	};

	return (
		<BaseControl
			label={label}
			id={id}
			__nextHasNoMarginBottom={true}
			hideLabelFromVision={hideLabelFromVision}
			help={help}
			className={classnames(className, 'storepress-components-search-control')}
		>
			<div className="storepress-components-search-control__input-wrapper">
				<input
					{...restProps}
					ref={searchRef}
					className="storepress-components-search-control__input"
					id={id}
					type="search"
					placeholder={placeholder}
					onChange={(event) => onChange(event.target.value)}
					onKeyDown={onKeyDown}
					autoComplete="off"
					value={value || ''}
				/>
				<div className="storepress-components-search-control__icon">
					{renderRightButton()}
				</div>
			</div>
		</BaseControl>
	);
}

function SearchResults({useKey, useValue, selected, suggestions, isMultiSelect, onSelect}) {

	const instanceId = useInstanceId(SearchResults);
	const id         = `storepress-components-search-result-item-${instanceId}`;

	const [selectedItem, setSelectedItem] = useState(selected)

	const handleMultiSelection = (id, isSelected) => {
		if (isSelected) {
			setSelectedItem((values) => {
				values.push(id)
				return [...new Set(values)]
			})
		}
		else {
			setSelectedItem((values) => values.filter((value) => value !== id))
		}
	}

	const handleSingleSelection = (id, isSelected) => {
		if (isSelected) {
			setSelectedItem([id])
		}
		else {
			setSelectedItem([])
		}
	}

	const handleSelected = (event) => {
		const {value, checked} = event?.target;

		if (isMultiSelect) {
			handleMultiSelection(value, checked)
		}
		else {
			handleSingleSelection(value, checked)
		}
	}

	const handleChecked = (selected, current) => {
		return selected.includes(current) || selected.includes(current?.toString())
	}

	useLayoutEffect(() => {
		onSelect(selectedItem)
	}, [selectedItem]);

	return suggestions.length > 0 ? (
		<div className="storepress-search-list-search-result-wrapper">
			<ul>
				{suggestions.map((suggestion, index) => {

					const key = get(suggestion, useKey)
					const value = get(suggestion, useValue)

					return (
						<li key={index} className="storepress-search-list-search-result-item">
							<label className="storepress-search-list-search-result-item__label">
								<input checked={handleChecked(selectedItem, key)} onChange={handleSelected} name={id} value={key} type={isMultiSelect ? 'checkbox' : 'radio'}/>
								<span className="storepress-search-list-search-result-item__title">{value}</span>
							</label>
						</li>
					)
				})}
			</ul>
		</div>
	) : ''
}

export function SearchList({
							   selected = [],
							   suggestions = [],
							   isLoading = false,
							   isMultiSelect = false,
							   hideSearchBox = false,
							   keyName = 'id',
							   valueName = 'title',
							   onSearch = (input) => {},
							   onSelect = (input) => {}
						   }) {

	const [currentValue, setCurrentValue] = useState('')

	const onStartTyping = (input) => {
		setCurrentValue(input)
		onSearch(input)
	}

	return (
		<div className="storepress-search-list-wrapper">
			{!hideSearchBox && (
				<div className="storepress-search-list-search-control-wrapper">
					<SearchControl
						isLoading={isLoading}
						onChange={onStartTyping}
						value={currentValue}
					/>
				</div>
			)}

			<SearchResults useKey={keyName} useValue={valueName} selected={selected} suggestions={suggestions} onSelect={onSelect} isMultiSelect={isMultiSelect}/>
		</div>
	);
}
