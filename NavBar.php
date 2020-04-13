//Display the nav bar and call this method on everypage
//Also set the font color of the link based on what page the user is currently at.

function displayNavBar($currentPage){
	<table id='table2' border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#123456">
  		<tr valign="bottom">
    			<td width="75%">
	if($currentPage == "ProjectList"){
		<?php echo "&nbsp;&nbsp<a href=ProjectList.php><font color=707070>Projects In Progress</font></a>
		&nbsp;&nbsp;|&nbsp;&nbsp;";?>
          	<?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
          	&nbsp;&nbsp;|&nbsp;&nbsp;";?>
          	<?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
          	&nbsp;&nbsp;|&nbsp;&nbsp;";?>
          	<?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
          	&nbsp;&nbsp;|&nbsp;&nbsp;";?>
          	<?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
          	&nbsp;&nbsp;|&nbsp;&nbsp;";?>
          	<?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
          	&nbsp;&nbsp;";?>
	}
	else if($currentPage == "ProjectQueue"){
                <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php><font color=707070>Projects In Queue</font></a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
                &nbsp;&nbsp;";?>
	}
        else if($currentPage == "ProjectComplete"){
                <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php><font color=707070>Projects Complete</font></a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
                &nbsp;&nbsp;";?>
        }
        else if($currentPage == "ProjectOn-Hold"){
                <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php><font color=707070>Projects On-Hold</font></a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
                &nbsp;&nbsp;";?>
        }
        else if($currentPage == "ITLList"){
                <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php><font color=707070>Projects ITL</font></a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
                &nbsp;&nbsp;";?>
        }
        else if($currentPage == "DevProjectsList"){
                <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php><font color=707070>Dev Projects</font></a>
                &nbsp;&nbsp;";?>
        }
        else{
                <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
                &nbsp;&nbsp;";?>
        }
	</td>
}

