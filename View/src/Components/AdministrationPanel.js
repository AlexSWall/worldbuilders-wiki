import React, { Component } from 'react';

class AdministrationPanel extends Component 
{
	render() {
		const authenticationData = this.props.authenticationData;
		return (
			<div className="card">
				<div className="card-header">
					<h1>Administration Panel — Home</h1>
				</div>
				<div className="card-body">
					<p style={{marginBottom: '20px'}}>Administrators only.</p>
					<p><u><b>Authentication Data.</b></u></p>
					<dl style={{listStyleType: 'none', marginLeft: '20px'}}>
						<dd><b>{authenticationData.isAuthenticated ? 'Is Authenticated. ✓' : 'Is Not Authenticated! ✗'}</b></dd>
						<dd><b>Preferred Name:</b></dd>
						<dt style={{marginLeft: '20px'}}>{authenticationData.userData.preferredName}</dt>
					</dl>
					{this.props.children}
				</div>
			</div>
		);
	}
}

export default AdministrationPanel;