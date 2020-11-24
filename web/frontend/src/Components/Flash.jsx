import React from 'react';

export default function Flash({ flash })
{
	return (
		<>
			{ flash.info && flash.info.length &&
				<div class='alert alert-info'>
					{ flash.info[0] }
				</div>
			}
			{ flash.error && flash.error.length &&
				<div class='alert alert-danger'>
					{ flash.error[0] }
				</div>
			}
		</>
	);
}
