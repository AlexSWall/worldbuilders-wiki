import React, { Component } from 'react';

import TextInput    from 'Form Components/TextInput'
import SubmitButton from 'Form Components/SubmitButton'
import CSRFField    from 'Form Components/CSRFField'

class DeleteWebpage extends Component 
{
	render() {
		return (
			<React.Fragment>
				<h1>Meta: Delete A Webpage</h1>

				<form action='Delete_Wiki_Page' method='post' autoComplete='off'>
					<TextInput
						formId='pageName'
						labelText='Page Name:'
					/>
					<SubmitButton text='Delete Webpage' />
					<CSRFField csrfHTML={this.props.csrfHTML} />
				</form>
			</React.Fragment>
		);
	}
}

export default DeleteWebpage;