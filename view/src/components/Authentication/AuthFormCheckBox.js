import React, { Component } from 'react';

class AuthFormCheckBox extends Component 
{
	render() {
		return (
			<div className='form-group form-check'>
				<input type='checkbox' className="form-check-input" name={this.props.formId} id={this.props.formId} />
				<label className='form-label form-check-label' htmlFor={this.props.formId}>{this.props.text}</label>
			</div>
		);
	}
}

export default AuthFormCheckBox;

