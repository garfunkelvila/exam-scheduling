<h3>Registered Accounts</h3>
Filter: <input class="my-input-2" style="width: 40%; min-width: 4in;" type="search" id="txbSearch" placeholder="User name or ID number" onkeyup="search(this.value)">
<br>
<br>
<div style="display: table; width: 100%">
	<div class="w3-container" style="display: table-row;">
		<div class="w3-border-bottom w3-border-blue" style="display: table-cell; min-width: 2in"><b>User Name</b></div>
		<div class="w3-border-bottom w3-border-blue" style="display: table-cell; width: 1.25in; min-width: 1.25in; text-align: center;"><b>ID</b></div>
		<div class="w3-border-bottom w3-border-blue" style="display: table-cell; width: 2in">
			<b>Account Type</b>
			<div class="w3-dropdown-hover" style="">
				<button class="my-button w3-hover-green"><i class="fas fa-filter" aria-hidden="true"></i></button>
				<div class="w3-dropdown-content w3-bar-block w3-card-4" style="right: 0;">
					<a class="w3-bar-item w3-button my-blue" id="accFilter-0" onclick="selectAccFilter('0')">All</a>
					<?php 
						$stmt = null;
						$stmt = $conn->prepare("SELECT * FROM `users_access_types` ORDER BY `Level` ASC;");
						$stmt->execute();
						$accessResult = $stmt->get_result();
						$x = 1;
						if ($accessResult->num_rows > 0) {
							while ($accesssRow = $accessResult->fetch_assoc()) {
								?><a class="w3-bar-item w3-button" id="accFilter-<?php echo $accesssRow["Id"] ?>" onclick="selectAccFilter('<?php echo $accesssRow["Id"] ?>')"><?php echo $accesssRow["Name"] ?></a><?php
							}
						}
					?>
				</div>
			</div>
		</div>
		<div class="w3-border-bottom w3-border-blue" style="display: table-cell; width: 2in;"><b>Date Registered</b></div>
		<div class="w3-border-bottom w3-border-blue" style="display: table-cell; width: 2in;"><b>Action</b></div>
	</div>
</div>
<div style="display: table; width: 100%" id="usersContainer">
	<!-- THIS THING HOLDS THE DATAS!!!! -->
</div>
<script type="text/javascript">
	var divUsersContainer = document.getElementById("usersContainer");
	var accFilter = 0;

	function selectAccFilter(type){
		$("#accFilter-" + accFilter).removeClass("my-blue");
		$("#accFilter-" + type).addClass("my-blue");

		accFilter = type;
		search($("#txbSearch").val());
	}

	function search(q){
		$("#usersContainer").html("<div style='margin: auto;' class='loader'></div> ");
		$.ajax({
			url: "ajax_table_users.php",
			data: {
				q: q,
				accFilter: accFilter},
			success: function(users){
				$("#usersContainer").html(users);
			}
		}
		);
		return false;
	}

	function btnEdit(id){
		document.getElementById("frmUser" + id).style.display = "table-row";
		document.getElementById("divView" + id).style.display = "none";
		document.getElementById("txbfName" + id).focus();
	}
	function btnDelete(id,userName){
		if (confirm("Are you sure you want to delete " + userName + "?") == true){
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function(){
				if (this.readyState == 4 && this.status == 200) {
					search('');
				}
			};
			xhttp.open("POST", "ajax_delete_user.php?q=" + id);
			xhttp.send();
		}
		return false;
	}
	function btnCommitEdit(id){
		//---------------------------------------------------------------------------------
		$.ajax({
			url: "ajax_update_user.php",
			dataType: "json",
			data: {
				idNumber: id,
				fName: $("#txbfName" + id).val(),
				mName: $("#txbmName" + id).val(),
				lName: $("#txblName" + id).val()
			},
			success: function(response){
				if(response.sucess){
					alert(response.result);
					search($("#txbSearch").val());
				}
			}
		});


		return false;
	}
	function btnCancelEdit(id){
		document.getElementById("frmUser" + id).style.display = "none";
		document.getElementById("divView" + id).style.display = "table-row";
		document.getElementById("txbfName" + id).value = document.getElementById("txbfName" + id).defaultValue;
		document.getElementById("txbmName" + id).value = document.getElementById("txbmName" + id).defaultValue;
		document.getElementById("txblName" + id).value = document.getElementById("txblName" + id).defaultValue;
	}

	function resetPassword(id){
		if (confirm("Are you sure you reset password for " + $("#txbfName" + id).val() + " " + $("#txblName" + id).val() + "?") == true){
			$.ajax({
				url: "ajax_update_password.php",
				dataType: "json",
				data: {userPassword: "abcd1234", id: id},
				success: function(response){
					if(response.sucess){
						alert('Password sucesfully reset');
						search($("#txbSearch").val());
					}
					else{
						alert('Password reset failed\nUser may still have the default password');
					}
				}
			});
		}
	}
	search('');
</script>