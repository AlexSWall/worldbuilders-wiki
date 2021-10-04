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

	// Notes:
	// On selecting a value, input's opacity is set to 1 to stop the blinking of
	// the select box. The input text is therefore displayed in an input
	// container directly above it, which overlays it.
	// I'll need to add this after.

	return (
		<div
			className='form-group'
			ref={ selectedValueRef }
		>
			<label>
				{ labelText }
			</label>

			{ ( inputContents === '' ) &&
				<div
					className={ 'form-select-selected-value' }
				>
					{ selectedOption }
				</div>
			}

			<input
				id={ formId }
				className={ 'form-select-input' }
				autoComplete={ 'off' }
				value={ inputContents }
				onFocus={ e => {
					console.log('Focused on input');
					setShowDropdown(true);
				} }
				onBlur={ e => {
					console.log('Blurring input');
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
					console.log('Changing input (to ' + value + ')');
					setInputContents(value);
				} }
			/>
			{ showDropdown &&
				<div className='form-select-dropdown'>
					{ options && options.filter(
							option => option.startsWith( inputContents )
						).map(
							( option, i ) =>
								<div
									key={ i }
									className='form-select-option'
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
	);
}
