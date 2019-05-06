import React, { Component } from 'react';

class AuthFormTextEntry extends Component 
{
	render() {
		return (
			<div className={ this.props.errors ? 'form-group is-invalid' : 'from-group>'}>
				<label className='form-label' htmlFor={this.props.formId}>{ this.props.labelText }</label>
				<input
					className='form-control'
					type={this.props.type}
					name={this.props.formId}
					id={this.props.formId}
					placeholder={this.props.placeholder}
					value={this.props.oldValue}
				/>
				{this.props.errors && <span className='help-block'>{this.props.errors[0]}</span>}
			</div>
		);
	}
}

export default AuthFormTextEntry;