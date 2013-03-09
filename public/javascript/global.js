$(function() {
	$(".eleveFields, .enseignantFields").hide();
	$("#eleve").click(function() {
		if ($(this).is(":checked")) {
			$(".eleveFields").show();
			$(".enseignantFields").hide();
		}
	});
	$("#enseignant").click(function() {
		if ($(this).is(":checked")) {
			$(".eleveFields").hide();
			$(".enseignantFields").show();
		}
	});
	$("#superviseur").click(function() {
		if ($(this).is(":checked")) {
			$(".eleveFields").hide();
			$(".enseignantFields").hide();
		}
	});
	$("#eleve").triggerHandler("click");
	$("#enseignant").triggerHandler("click");
	$("#superviseur").triggerHandler("click");
	
	$("#hasDateReportNo").click(function() {
		if ($(this).is(":checked")) {
			$("[name^='heure_report_'], [name='date_report']").attr("disabled", "disabled");
		}
	});
	$("#hasDateReportYes").click(function() {
		if ($(this).is(":checked")) {
			$("[name^='heure_report_'], [name='date_report']").removeAttr("disabled");
		}
	});
	
	$("#hasDateReportNo").triggerHandler("click");
	$("#hasDateReportYes").triggerHandler("click");
	
	$(".listingContainer").find("tr:even").find("td").addClass("even");
	/* datepicker */
	$(".date").datepicker(
		{
			buttonText: 			"Choisir...",
			constrainInput: 		true,
			dateFormat: 			"dd/mm/yy",
			firstDay: 				1,
			//gotoCurrent: 			true,
			showAnim: 				"drop",
			showButtonPanel: 		true,
			//showOn: 				"both",
			showOptions: 			{ direction: "left" }
		},
		$.datepicker.regional["fr"]
	);
	
	/* drag 'n drop */
	$(".event[id!=''], .eventPending[id!=''], .eventConfirmed[id!='']").draggable({
		cursor : 			"move",
		containment: 		"parent",
		handle: 			".moveableTip",
		grid: 				[133, 22],
		opacity: 			0.6,
		start: function(event, ui) {
			$(this).data("left", $(this).css("left"));
			$(this).data("top", $(this).css("top"));
		},
		stop: function(event, ui) {
			var self = $(this);
			if ((self.data("left") == self.css("left")) && (self.data("top") == self.css("top"))) {
				return;
			}
			if (confirm("Etes-vous sûr de vouloir déplacer ce cours ?")) {
				$.post(
					"index.php?page=emploi_du_temps&action=drag_drop",
					{
						left: parseInt(self.css("left"), 10),
						top: parseInt(self.css("top"), 10),
						id: self.attr("id")
					},
					function(data) {
						var json = eval("(" + data + ")");
						
						if (json.result == "deleted") {
							self.attr("class", "event");
							self.find(".reportLabel").html("");
							self.find(".acceptRefuseReportLink").remove();
							self.attr("id", json.id);
							self.find(".hourLabel").html(json.heureReport + " - " + json.heureReportFin);
						} else if ((json.result == "updated") || (json.result == "created")) {
							if (json.result == "created") {
								self.find(".reportLabel").html("(report du " + json.dateOrigine + ")");
							}
							if (json.etat == "validée") {
								self.attr("class", "eventConfirmed");
								self.find(".acceptRefuseReportLink").remove();
							} else if (json.etat == "en attente") {
								self.attr("class", "eventPending");
							}
							self.attr("id", json.id);
							self.find(".hourLabel").html(json.heureReport + " - " + json.heureReportFin);
						} else {
							self.css("left", self.data("left"));
							self.css("top", self.data("top"));
						}
					}
				);
			} else {
				$(this).css("left", $(this).data("left"));
				$(this).css("top", $(this).data("top"));
			}
		}
	});
	
	$("input[name='showReservations']").change(function() { $(this).parent().submit(); });
	
	/* Upload */
	$("#form_upload").submit(function() {
		$("#progressbar").progressbar({value: 0});
		attendEnvoie();
	});
	
	
	/* instanciation de la fenêtre */
	$("#dialog").dialog({
		autoOpen : 		false,
		draggable: 		true,
		minHeight: 		50,
		modal: 			true,
		position: 		"center",
		resizable: 		false,
		width: 			290
	});
	
	/* réservations */
	$(".acceptReservationLink").live("click", function() {
		var rel = $(this).attr("rel");
		$.post(
			"index.php?page=reservations&action=accept&id=" + rel,
			function(data) {
				self = $(".acceptRefuseReservationLink[rel='" + rel + "']");
				self.parent().removeClass("event eventPending eventRefused eventConfirmed");
				self.parent().addClass("eventConfirmed");
				self.remove();
				$("#dialog").dialog("close");
			}
		);
		return false;
	});
	
	$(".refuseReservationLink").live("click", function() {
		var rel = $(this).attr("rel");
		$.post(
			"index.php?page=reservations&action=reject&id=" + rel,
			function(data) {
				self = $(".acceptRefuseReservationLink[rel='" + rel + "']");
				self.parent().remove();
				$("#dialog").dialog("close");
			}
		);
		return false;
	});
	
	$(".acceptRefuseReservationLink").click(function() {
		$("#dialog").dialog("option", "title", "Validation de la réservation");
		$("#dialog").html('<p><a class="acceptReservationLink" href="#" rel="' + $(this).attr("rel") + '" target="_self">Accepter</a><a class="refuseReservationLink" href="#" rel="' + $(this).attr("rel") + '" target="_self">Refuser</a></p>');
		$("#dialog").dialog("open");
		return false;
	});
	
	/* reports */
	$(".acceptReportLink").live("click", function() {
		var rel = $(this).attr("rel");
		$.post(
			"index.php?page=emploi_du_temps&action=accept_demande&id=" + rel + "&ajax=1",
			function(data) {
				self = $(".acceptRefuseReportLink[rel='" + rel + "']");
				self.parent().removeClass("event eventPending eventRefused eventConfirmed");
				self.parent().addClass("eventConfirmed");
				self.remove();
				$("#dialog").dialog("close");
				window.location.reload();
			}
		);
		return false;
	});
	
	$(".refuseReportLink").live("click", function() {
		var rel = $(this).attr("rel");
		$.post(
			"index.php?page=emploi_du_temps&action=reject_demande&id=" + rel + "&ajax=1",
			function(data) {
				self = $(".acceptRefuseReportLink[rel='" + rel + "']");
				self.parent().remove();
				$("#dialog").dialog("close");
				window.location.reload();
			}
		);
		return false;
	});
	
	$(".acceptRefuseReportLink").click(function() {
		$("#dialog").dialog("option", "title", "Validation du report");
		$("#dialog").html('<p><a class="acceptReportLink" href="#" rel="' + $(this).attr("rel") + '" target="_self">Accepter</a><a class="refuseReportLink" href="#" rel="' + $(this).attr("rel") + '" target="_self">Refuser</a></p>');
		$("#dialog").dialog("open");
		return false;
	});
	
	$(".subject_id").live("change", function() {
		var currentId = $(this).next("input[name^='event']").val().split("-");
		$(this).next("input[name^='event']").val(currentId[0] + "-" + currentId[1] + "-" + currentId[2] + "-" + $(this).val() + (currentId.length == 5 ? "-" + currentId[4] : ""));
	});
	
	$(".deleteCours").live("click", function() {
		$(this).parent().remove();
		return false;
	});
	
	function SetDraggable() {
		$(".draggable").resizable({
			grid: 22,
			handles : "s",
			maxHeight: 158,
			minHeight: 44,
			resize: function(event, ui) {
				var PART_HEIGHT = 21;
				
				var heure_debut_m = ((parseInt($(this).css("top"), 10) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) % 60;
				var heure_debut_h = (((parseInt($(this).css("top"), 10) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) - heure_debut_m) / 60;
				
				// Le +7 représente l'épaisseur de la bordure, un éventuel margin et autres...
				var heure_fin_m = (((parseInt($(this).css("top"), 10) + parseInt($(this).css("height"), 10) + 7) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) % 60;
				var heure_fin_h = ((((parseInt($(this).css("top"), 10) + parseInt($(this).css("height"), 10) + 7) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) - heure_fin_m) / 60;
				
				heure_debut_m = heure_debut_m < 10 ? "0" + heure_debut_m : heure_debut_m;
				heure_debut_h = heure_debut_h < 10 ? "0" + heure_debut_h : heure_debut_h;
				
				heure_fin_m = heure_fin_m < 10 ? "0" + heure_fin_m : heure_fin_m;
				heure_fin_h = heure_fin_h < 10 ? "0" + heure_fin_h : heure_fin_h;
				
				$(this).find(".hourLabel").html(heure_debut_h + ":" + heure_debut_m + " - " + heure_fin_h + ":" + heure_fin_m);
				
				var currentId = $(this).find("input[name^='event']").val().split("-");
				$(this).find("input[name^='event']").val(currentId[0] + "-" + heure_debut_h + ":" + heure_debut_m + "-" + heure_fin_h + ":" + heure_fin_m + "-" + currentId[3] + (currentId.length == 5 ? "-" + currentId[4] : ""));
			}
		});
		var jour;
		$(".draggable").draggable({
			cursor : 			"move",
			containment : 		"parent",
			grid : 				[133, 22],
			opacity : 			0.6,
			stop : function(event, ui) {
				//alert(jour);
				//alert($(this).find("input[name^='event']").val());
			},
			drag : function(event, ui) {
				var PART_HEIGHT = 21;
				var SUBJECT_WIDTH = 126;
				
				var heure_debut_m = ((parseInt($(this).css("top"), 10) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) % 60;
				var heure_debut_h = (((parseInt($(this).css("top"), 10) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) - heure_debut_m) / 60;
				
				// Le +7 représente l'épaisseur de la bordure, un éventuel margin et autres...
				var heure_fin_m = (((parseInt($(this).css("top"), 10) + parseInt($(this).css("height"), 10) + 7) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) % 60;
				var heure_fin_h = ((((parseInt($(this).css("top"), 10) + parseInt($(this).css("height"), 10) + 7) / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) - heure_fin_m) / 60;
				
				heure_debut_m = heure_debut_m < 10 ? "0" + heure_debut_m : heure_debut_m;
				heure_debut_h = heure_debut_h < 10 ? "0" + heure_debut_h : heure_debut_h;
				
				heure_fin_m = heure_fin_m < 10 ? "0" + heure_fin_m : heure_fin_m;
				heure_fin_h = heure_fin_h < 10 ? "0" + heure_fin_h : heure_fin_h;
				
				$(this).find(".hourLabel").html(heure_debut_h + ":" + heure_debut_m + " - " + heure_fin_h + ":" + heure_fin_m);
				
				// On récupère l'ID actuel du cours pour y changer les composantes
				var currentId = $(this).find("input[name^='event']").val().split("-");
				
				// On récupère la "left" de la matière pour déterminer le jour
				var left = parseInt($(this).css("left"), 10);
				jour = 1;
				switch (left) {
					case 1 :   jour = 1; break;
					case 134 : jour = 2; break;
					case 267 : jour = 3; break;
					case 400 : jour = 4; break;
					case 533 : jour = 5; break;
				}
				// On modifie l'ID du cours en mettant à jour ses composantes
				$(this).find("input[name^='event']").val(jour + "-" + heure_debut_h + ":" + heure_debut_m + "-" + heure_fin_h + ":" + heure_fin_m + "-" + currentId[3] + (currentId.length == 5 ? "-" + currentId[4] : ""));
			}
		});
	}
	var name = $(".event").length;
	SetDraggable();
	$(".create").click(function(e) {
		$(".calendarWrapper").append('<div class="event draggable" style="position: absolute; height: 81px; left: 1px; top: 0px; width: 126px;"><a class="deleteLink deleteCours" href="#"></a><p class="hourLabel">08:30 - 10:30</p><select class="subject_id">' + $(".subject_id").html() + '</select><input name="event' + name + '" type="hidden" value="1-08:30-10:30-' + $(".subject_id").find("option:first").val() + '" /></div>');
		$(".event:last").effect("highlight");
		SetDraggable();
		name++;
		return false;
	});
	
	$("#type_periode").change(function() {
		if ($(this).val() == "vacances") {
			$(".calendarContainer, .addLink").hide();
		} else {
			$(".calendarContainer, .addLink").show();
		}
	});
	$("#type_periode").triggerHandler("change");
	
	$("#autosubmit_select").change(function() { $(this).parents("form:first").submit(); });
});