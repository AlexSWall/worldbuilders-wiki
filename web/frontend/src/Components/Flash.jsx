import React from 'react';

import { GlobalConsumer } from '../GlobalStore';

export default function Flash()
{
	return (
		<GlobalConsumer>
			{ globals => (
				<>
					{ globals.flash.info && globals.flash.info.length && (
						<div class='alert alert-info'>
							{ globals.flash.info[0] }
						</div>
					)}
					{ globals.flash.error && globals.flash.error.length && (
						<div class='alert alert-danger'>
							{ globals.flash.error[0] }
						</div>
					)}
				</>
			)}
		</GlobalConsumer>
	);
}
