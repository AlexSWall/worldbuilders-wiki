import React, { Component } from 'react';

class AuthFormTextEntry extends Component 
{
	constructor(props) {
		super(props);
		this.state = {
			value: this.props.oldValue || ''
		};
	}

	onChange = (event) => {
		this.setState({value: event.target.value});
	};

	render() {
		return (
			<div className={this.props.errors ? 'form-group is-invalid' : 'form-group'}>
				<label className='form-label' htmlFor={this.props.formId}>{ this.props.labelText }</label>
				<input
					className={this.props.errors ? 'form-control has-error' : 'form-control'}
					type={this.props.type}
					name={this.props.formId}
					id={this.props.formId}
					value={this.state.value}
					placeholder={this.props.placeholder}
					onChange={this.onChange}
				/>
				{this.props.errors && <span className='help-block'>{this.props.errors}</span>}
			</div>
		);
	}
}

export default AuthFormTextEntry;