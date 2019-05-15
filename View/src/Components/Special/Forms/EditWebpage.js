import React, { Component } from 'react';

import FormTextInput from './Form Components/FormTextInput'
import FormTextArea from './Form Components/FormTextArea'
import FormSubmitButton from './Form Components/FormSubmitButton'

class EditWebpage extends Component 
{
	render() {
		return (
			<React.Fragment>
				<h1>Meta: Edit A Webpage</h1>
				<p>For help with writing in the wiki templating style, see <a href="/#Special:Template_Formatting">Template Formatting</a>.</p>
				<p>To delete a webpage, use the <a href='/#Special:Delete_Wiki_Page'>Delete Wiki Page</a> webpage instead.</p>

				<form action='Edit_Wiki_Page' method='post' autoComplete='off'>
					<FormTextInput
						formId='pageName'
						labelText='Page Name:'
					/>
					<FormTextArea
						formId='webpageTemplate'
						labelText='Webpage Template Content'
					/>
					<FormSubmitButton text='Edit Webpage' />
				</form>
			</React.Fragment>
		);
	}
}

export default EditWebpage;