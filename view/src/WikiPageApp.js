import React, { Component } from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import WikiPanel from './Components/WikiPanel';
import Footer from './Components/Footer';
import Sidebar from './Components/Sidebar';

class WikiPageApp extends Component
{
	render()
	{
		return (
			<div id="pageWrapper">
				<NavigationBar auth={this.props.auth}/>
				<Flash flash={this.props.flash}/>
				<main>
					<div id="contentWrapper">
						<div id="content">
							<div id="mainPanelWrapper">
								<div id="mainPanel">
									<WikiPanel />
								</div> {/* mainPanelWrapper */}
							</div> {/* mainPanel */}
						</div> {/* content */}
					</div> {/* contentWrapper */}
				</main> {/* main */}
				<Footer />
				<Sidebar />
			</div> /* pageWrapper */
		);
	}
}

export default WikiPageApp;