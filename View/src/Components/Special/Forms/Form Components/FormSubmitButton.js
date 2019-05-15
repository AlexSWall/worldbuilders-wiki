import React, { Component } from 'react';

class FormSubmitButton extends Component 
{
	render() {
		return (
			<button type='submit' className='form-submit'>{this.props.text}</button>
		);
	}
}

export default FormSubmitButton;