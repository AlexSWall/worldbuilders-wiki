import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

export default function Flash()
{
	const globals = useContext(GlobalsContext);

	return (
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
	);
}
