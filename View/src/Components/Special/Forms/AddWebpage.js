import React, { Component } from 'react';

import TextInput    from 'Form Components/TextInput'
import SubmitButton from 'Form Components/SubmitButton'
import CSRFField    from 'Form Components/CSRFField'

class AddWebpage extends Component 
{
	render() {
		return (
			<React.Fragment>
				<h1>Meta: Add A Webpage</h1>
				<p>For help with writing in the wiki templating style, see <a href="/#Special:Template_Formatting">Template Formatting</a>.</p>

				<form action='Add_Wiki_Page' method='post' autoComplete='off'>
					<TextInput
						formId='pageName'
						labelText='Page Name:'
					/>
					<SubmitButton text='Add Webpage' />
					<CSRFField csrfHTML={this.props.csrfHTML} />
				</form>
			</React.Fragment>
		);
	}
}

export default AddWebpage;