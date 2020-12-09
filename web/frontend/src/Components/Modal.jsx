import React, { useCallback, useEffect, useRef } from 'react'
import ReactDom from 'react-dom'

export default function Modal({ isOpen, setOpen, children })
{
	const modalBackground = useRef(null);

	const handleOutsideClick = useCallback( e =>
		{
			if ( modalBackground.current.contains(e.target) )
				setOpen(false);
		},
		[setOpen]
	);

	useEffect(() =>
		{
			if (isOpen)
			{
				console.log('Rendering Modal open');
				window.addEventListener('click', handleOutsideClick);
			}
			else
			{
				console.log('Rendering Modal closed');
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
