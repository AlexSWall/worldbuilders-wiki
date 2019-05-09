import React, { Component } from 'react';

class Flash extends Component 
{
	render() {
		const flash = this.props.flash;
		return (
			<React.Fragment>
				{ flash.info && flash.info.length &&
					<div class='alert alert-info'>
						{flash.info[0]}
					</div>
				}
				{ flash.error && flash.error.length &&
					<div class='alert alert-danger'>
						{flash.error[0]}
					</div>
				}
			</React.Fragment>
		);
	}
}

export default Flash;