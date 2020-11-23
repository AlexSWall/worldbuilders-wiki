import React, { Component } from 'react';

class TextArea extends Component 
{
	constructor(props) {
		super(props);
		this.state = {
			value: ''
		};
	}

	onChange = (event) => {
		this.setState({value: event.target.value});
	};

	render() {
		return (
			<div className='form-group'>
				<label className='form-label' htmlFor={this.props.formId}>{ this.props.labelText }</label>
				<textarea
					className='form-control'
					type='text'
					name={this.props.formId}
					id={this.props.formId}
					value={this.state.value}
					onChange={this.onChange}
				/>
			</div>
		);
	}
}

export default TextArea;