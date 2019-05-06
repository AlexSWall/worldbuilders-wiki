import React, { Component } from 'react';

import NavigationBar from './components/NavigationBar';
import Flash from './components/Flash';
import WikiPanel from './components/WikiPanel';
import Footer from './components/Footer';
import Sidebar from './components/Sidebar';

class App extends Component
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
									<WikiPanel rawWebpageContent={this.props.wiki.webpageContent}/>
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

export default App;