import React, { MutableRefObject, useEffect, useState, useRef, ReactElement } from 'react';

import classNames from 'classnames';

interface Props
{
	formId: string;
	labelText: string;
	width: number;
	hasError: boolean;
	setValue: ( value: string ) => void;
	options: string[];
	initialValue: string | null;
};

export const SelectDropdown = ({ formId, labelText, width, hasError, setValue, options, initialValue }: Props): ReactElement =>
{
	// Contains the ephemeral contents of the input; cleared on blur.
	const [ inputContents, setInputContents ] = useState<string>( '' );

	// Contains any valid value selected, obtained from the input on its blur.
	// This is shown whenever inputContents is empty.
	const [ selectedOption, setSelectedOption ] = useState<string | null>( null );

	// Ensure selected option is updated if initialValue is changed
	useEffect( () => {
		if ( initialValue !== null )
		{
			setSelectedOption( initialValue );
		}
	}, [ initialValue ]);

	// Whether to show the dropdown. This is true whenever the input has focus,
	// and false when the input is subsequently blurred.
	const [ showDropdown, setShowDropdown ] = useState<boolean>( false );

	// Used to switch focus from clicking dropdown to non-input. This ensured the
	// input blurs, and also ensures we don't immediately select it again.
	const selectedValueRef = useRef<HTMLDivElement>( null ) as MutableRefObject<HTMLDivElement>;

	const filteredOptions = options.filter(
		option => option.toLowerCase().startsWith( inputContents.toLowerCase() )
	);

	return (
		<div
			className='form-group'
			ref={ selectedValueRef }
			style={ {
				width: width
			} }
		>
			<div className='form-input-wrapper'>
				<div className='form-select-wrapper'>
					<div
						className={ classNames({
							'form-select-selected-value': true,
							'form-select-hidden': inputContents !== ''
						}) }
						style={ {
							color: 'rgb(51, 51, 51, ' + ( ( inputContents !== '' ) ? '0' : ! selectedOption ? '0.5' : '1' )
						} }
					>
						{ selectedOption }
					</div>

					<input
						id={ formId }
						className={ classNames({
							'form-select-input': true,
							'has-content': inputContents || selectedOption,
							'form-input-has-error': hasError
						}) }
						autoComplete='off'
						value={ inputContents }
						onFocus={ _e => {
							setShowDropdown(true);
						} }
						onBlur={ e => {
							setShowDropdown(false);
							if ( e && options.includes( inputContents ) )
							{
								setSelectedOption( inputContents );
								setValue( inputContents );
							}
							setInputContents('');
						} }
						onChange={ e => {
							const value = e.target.value;
							setInputContents(value);
						} }
					/>

					<label htmlFor={ formId }>{ labelText }</label>
					<span className="focus-border">
						<i></i>
					</span>

					{ showDropdown && filteredOptions && ( filteredOptions.length > 0 ) &&
						<div className='form-select-dropdown'>
							{ filteredOptions.map(
									( option, i ) =>
										<div
											key={ i }
											className={ classNames({
												'form-select-option-selected': ( option === selectedOption ),
												'form-select-option': ( option !== selectedOption ),
											}) }
											onMouseDown={ () => {
												selectedValueRef.current.focus();
												setInputContents( option );
											} }
										>
											{ option }
										</div>
								) }
						</div>
					}
				</div>
			</div>
		</div>
	);
};
