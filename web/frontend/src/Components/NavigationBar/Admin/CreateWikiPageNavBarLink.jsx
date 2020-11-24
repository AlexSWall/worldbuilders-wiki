import React, { useState } from 'react';

import NavBarLink from '../NavBarLink';

import Modal from '../../Modal';

export default function CreateWikiPageNavBarLink()
{
	const [isOpen, setOpen] = useState(false);

	return (
		<>
			<NavBarLink onClick={ () => setOpen(true) } text="Add Page" active={ false } />
			<Modal open={ isOpen } onClose={ () => setOpen(false) }>
				Foo bar
			</Modal>
		</>
	);
}

