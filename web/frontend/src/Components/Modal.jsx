import React, { useCallback, useEffect, useRef } from 'react'
import ReactDom from 'react-dom'

const MODAL_STYLES = {
	position: 'fixed',
	top: '50%',
	left: '50%',
	transform: 'translate(-50%, -50%)',
	backgroundColor: '#FFF',
	padding: '50px',
	zIndex: 1000,

	MozBorderRadius: '5px',
	WebkitBorderRadius: '5px',
	KhtmlBorderRadius: '5px',
};

const OVERLAY_STYLES = {
	position: 'fixed',
	top: 0,
	left: 0,
	right: 0,
	bottom: 0,
	backgroundColor: 'rgba(0, 0, 0, .7)',
	zIndex: 1000
};

const X_STYLES = {
	position: 'absolute',
	cursor: 'pointer',

	top: '-10px',
	right: '-10px',

	boxSizing: 'border-box',
	width: '20px',
	height: '20px',

	borderWidth: '3px',
	borderStyle: 'solid',
	borderColor: '#605F61',
	borderRadius: '100%',

	background: '-webkit-linear-gradient(-45deg, transparent 0%, transparent 46%, white 46%,  white 56%,transparent 56%, transparent 100%), -webkit-linear-gradient(45deg, transparent 0%, transparent 46%, white 46%,  white 56%,transparent 56%, transparent 100%)',
	backgroundColor: '#605F61'
};

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
				window.addEventListener("click", handleOutsideClick);
			}
			else
			{
				console.log('Rendering Modal closed');
				window.removeEventListener("click", handleOutsideClick);
			}

			return () => window.removeEventListener("click", handleOutsideClick);
		},
		[isOpen, handleOutsideClick]
	);

	if (!isOpen)
	{
		window.removeEventListener("click", handleOutsideClick);
		return null;
	}

	return ReactDom.createPortal(
		<>
			<div style={ OVERLAY_STYLES } ref={ modalBackground }/>
			<div style={ MODAL_STYLES }>
				<button style={ X_STYLES } type="button" onClick={ () => setOpen(false) }></button>
				{ children }
			</div>
		</>,
		document.getElementById('portal')
	);
}
