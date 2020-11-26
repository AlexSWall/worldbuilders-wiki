import React, { useState } from 'react';

import NavBarLink from '../NavBarLink';

import Modal from '../../Modal';
import TextInput from '../../Form Components/TextInput';
import SubmitButton from '../../Form Components/SubmitButton';
import CSRFField from '../../Form Components/CSRFField';

export default function CreateWikiPageNavBarLink({ csrfHTML })
{
	const [isModalOpen, setModalOpen] = useState(false);

	return (
		<>
			<NavBarLink
				onClick={ () => setModalOpen(true) }
				text="Add Page"
				active={ false }
			/>

			<Modal isOpen={ isModalOpen } setOpen = { setModalOpen }>
				<form action='/a/Wiki_Page' method='post' autoComplete='off'>
					<TextInput formId='page_name' labelText='Page Name' />
					<SubmitButton text='Create Page' />
					<button type="button" onClick={ () => setModalOpen(false) }>Close Modal</button>
					<CSRFField csrfHTML={ csrfHTML }/>
				</form>
			</Modal>
		</>
	);
}
