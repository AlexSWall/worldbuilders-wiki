import React, { useState, useRef } from 'react';

import { Field, ErrorMessage } from 'formik';

export default function SelectDropdown({ formId, labelText, width, hasError, setFieldTouched, setValue, options, defaultText = '' })
{
	// Contains the ephemeral contents of the input; cleared on blur.
	const [ inputContents, setInputContents ] = useState( '' );

	// Contains any valid value selected, obtained from the input on its blur.
	// This is shown whenever inputContents is empty.
	const [ selectedOption, setSelectedOption ] = useState( null );

	// Whether to show the dropdown. This is true whenever the input has focus,
	// and false when the input is subsequently blurred.
	const [ showDropdown, setShowDropdown ] = useState( false );

	// Used to switch focus from clicking dropdown to non-input. This ensured the
	// input blurs, and also ensures we don't immediately select it again.
	const selectedValueRef = useRef(null);

	// This is checked for being non-empty on the dropdown being sought.
	const filteredOptions = options && options.filter(
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
						className={ 'form-select-selected-value' + ( ( inputContents !== '' ) ? ' form-select-hidden' : '' ) }
						style={ {
							color: 'rgb(51, 51, 51, ' + ( ( inputContents !== '' ) ? '0' : ! selectedOption ? '0.5' : '1' )
						} }
					>
						{ selectedOption }
					</div>

					<input
						id={ formId }
						className={ (hasError ? 'form-input-has-error ' : '') + ( inputContents || selectedOption ? 'has-content ' : '' ) + 'form-select-input' }
						autoComplete='off'
						value={ inputContents }
						onFocus={ e => {
							setShowDropdown(true);
						} }
						onBlur={ e => {
							setShowDropdown(false);
							if ( e !== '' && options && options.includes( inputContents ) )
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
											className={ 'form-select-option'
												+ ( ( option == selectedOption ) ? '-selected' : '' ) }
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
}
