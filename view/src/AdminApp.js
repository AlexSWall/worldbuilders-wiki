import React, { Component } from 'react';

import NavigationBar from './components/NavigationBar';

import AdminPanel from './components/AdminPanel';

class AdminApp extends Component
{
	render()
	{
		return (
			<div id="pageWrapper">
				<NavigationBar auth={this.props.auth}/>
				<main>
					<div id="contentWrapper">
						<div id="content">
							<AdminPanel />
						</div> {/* content */}
					</div> {/* contentWrapper */}
				</main> {/* main */}
			</div> /* pageWrapper */
		);
	}
}

export default AdminApp;