import React, { Component } from 'react';

import FormTextInput from './Form Components/FormTextInput'
import FormSubmitButton from './Form Components/FormSubmitButton'

class AddWebpage extends Component 
{
	render() {
		return (
			<React.Fragment>
				<h1>Meta: Add A Webpage</h1>
				<p>For help with writing in the wiki templating style, see <a href="/#Special:Template_Formatting">Template Formatting</a>.</p>

				<form action='Add_Wiki_Page' method='post' autoComplete='off'>
					<FormTextInput
						formId='pageName'
						labelText='Page Name:'
					/>
					<FormSubmitButton text='Add Webpage' />
				</form>
			</React.Fragment>
		);
	}
}

export default AddWebpage;