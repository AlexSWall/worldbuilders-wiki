import React, { Component } from 'react';

import FormTextInput from './Form Components/FormTextInput'
import FormSubmitButton from './Form Components/FormSubmitButton'

class DeleteWebpage extends Component 
{
	render() {
		return (
			<React.Fragment>
				<h1>Meta: Delete A Webpage</h1>

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

export default DeleteWebpage;