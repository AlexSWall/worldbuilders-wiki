import React, { useCallback, useEffect, useRef } from 'react'
import ReactDom from 'react-dom'

export default function Modal({ isOpen, setOpen, getHasUnsavedState, children })
{
	const modalBackground = useRef(null);

	const handleOutsideClick = useCallback( e =>
		{
			// Return early if the click we've hooked is inside our modal
			if ( ! modalBackground.current.contains(e.target) )
			{
				return;
			}

			// We have a click outside of the modal; check whether we have unsaved
			// changes to get a user confirmation prompt
			if ( getHasUnsavedState !== null && getHasUnsavedState() )
			{
				const msg = 'You may have unsaved changes; are you sure you want to close?';
				const confirm = window.confirm(msg);
				if ( ! confirm ) {
					// The user selected 'Cancel', so return early to avoid closing
					// the modal.
					return;
				}
			}

			// We've not aborted, so proceed with closing the modal
			setOpen(false);
		},
		[setOpen]
	);

	useEffect(() =>
		{
			if (isOpen)
			{
				window.addEventListener('click', handleOutsideClick);
			}
			else
			{
				window.removeEventListener('click', handleOutsideClick);
			}

			return () => window.removeEventListener('click', handleOutsideClick);
		},
		[isOpen, handleOutsideClick]
	);

	if (!isOpen)
	{
		window.removeEventListener('click', handleOutsideClick);
		return null;
	}

	return ReactDom.createPortal(
		<>
			<div id='modal-background' ref={ modalBackground }/>
			<div id='modal-pane'>
				<button id='modal-x-button' type='button' onClick={ () => setOpen(false) }></button>
				{ children }
			</div>
		</>,
		document.getElementById('portal')
	);
}
