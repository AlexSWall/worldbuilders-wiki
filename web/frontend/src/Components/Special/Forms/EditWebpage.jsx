import React from 'react';

import TextInput    from 'Form Components/TextInput'
import TextArea     from 'Form Components/TextArea'
import SubmitButton from 'Form Components/SubmitButton'
import CSRFField    from 'Form Components/CSRFField'

export default function EditWebpage({ csrfHTML })
{
	return (
		<React.Fragment>
			<h1>Meta: Edit A Webpage</h1>
			<p>For help with writing in the wiki templating style, see <a href="/#Special:Template_Formatting">Template Formatting</a>.</p>
			<p>To delete a webpage, use the <a href='/#Special:Delete_Wiki_Page'>Delete Wiki Page</a> webpage instead.</p>

			<form action='Edit_Wiki_Page' method='post' autoComplete='off'>
				<TextInput
					formId='pageName'
					labelText='Page Name:'
				/>
				<TextArea
					formId='webpageTemplate'
					labelText='Webpage Template Content'
				/>
				<SubmitButton text='Edit Webpage' />
				<CSRFField csrfHTML={ csrfHTML } />
			</form>
		</React.Fragment>
	);
}
