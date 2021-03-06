<?php defined('SHIELDON_VIEW') || exit('Life is short, why are you wasting time?');
/*
 * This file is part of the Shieldon package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use function Shieldon\Helper\_e;

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link rel="icon" href="data:,">
    <meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= _e('panel', 'login_heading_login', 'Login to Firewall Panel'); ?></title>
    <?php echo '<style>' . $css . '</style>'; ?>

    <style>
        
        .text-center {
            text-align: center;
        }

        .form-input {
            width: 100%;
            height: 40px;
            line-height: 40px;
            font-size: 13px;
            box-sizing: border-box;
            padding: 0 20px;
            background-color: #f1f1f1;
            border: 1px #eeeeee solid;
        }

        .input-box {
            padding: 5px 20px;
            overflow: hidden;
        }

        .btn-submit {
            width: 100%;
            height: 40px;
            line-height: 40px;
            font-size: 13px;
            color: #fff;
            font-weight: bold;
            box-sizing: border-box;
            box-shadow: inset 0px 1px 0px 0px #dcecfb;
            background: linear-gradient(to bottom, #61b0ff 5%, #4c99e0 100%);
	        background-color: #61b0ff;
            border: 1px solid #84bbf3;
            text-shadow: 0px 1px 0px #528ecc;
            cursor:pointer;
        }

        .btn-submit:hover {
            background:linear-gradient(to bottom, #4c99e0 5%, #61b0ff 100%);
	        background-color:#4c99e0;
        }

        .btn-submit:active {
            position: relative;
            top: 1px;
        }

        .logo {
            height: 30px;
        }

        .logo-wrapper {
          
        }

        .main-content {
            padding: 10px;
        }
        
        .error-notice {
            border: 1px #eb4141 solid;
            padding: 10px;
            color: #eb4141;
            margin: 20px;
            font-weight: bold;
        }

        html {
            height: 100%;
        }

        body {
            position: relative;
            background: #23a6d5;
            height: 100%;
        }

    </style>
</head>
<body>
    <div id="wrapper" class="wrapper">
		<div class="inner">
			<div class="card">
				<div class="card-header">
                    <div class="logo-wrapper">
                        <img src="<?php echo SHIELDON_PLUGIN_URL . 'src/assets/images/logo.png'; ?>" lass="logo">
                    </div>
                </div>
				<div class="card-body">
                    <form method="post" autocomplete="off">
                        <div class="main-content">
                            <?php if (! empty($error)) : ?>
                            <div class="error-notice">
                                <?php echo $error; ?>
                            </div>
                            <?php endif; ?>
                            <div class="input-box">
                                <input type="text" name="s_user" placeholder="Username" class="form-input" />
                            </div>
                            <div class="input-box">
                                <input type="password" name="s_pass" placeholder="Password" class="form-input" />
                            </div>
                            <?php if (! empty($this->captcha)) : ?>
                            <div class="input-box">
                                <?php foreach ($this->captcha as $captcha) : ?>
                                    <?php echo $captcha->form(); ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <div class="input-box">
                                <button type="submit" class="btn-submit"><?= _e('panel', 'login_btn_login', 'Login'); ?></button>
                            </div>
                        </div>
                        <?php $this->_csrf(); ?>
                    </form>
				</div>
            </div>
        </div> 
    </div>
</body>
</html>
