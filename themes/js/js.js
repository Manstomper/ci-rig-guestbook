jQuery(document).ready(function($) {
	
	$("#bodyindex #messageForm").append($("<span/>").html("&#215").click(function() {
		$("#messageForm")[0].reset();
		$("#messageForm").hide();
		Recaptcha.reload();
	}));
	
	$("#bodyindex #messageForm").submit(function(e) {
		var methodName = e.target.getAttribute("data-method");
		var data = {method: methodName};
		
		e.preventDefault();
		
		data.key = $("#data").attr("data-key");
		
		$.each($("#messageForm").serializeArray(), function(key, obj) {
			data[obj.name] = obj.value;
		});
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $("#data").attr("data-baseurl") + "api/message",
			data: data
		})
		.done(function(response) {
			$("#messageForm")[0].reset();
			$("#messageForm").hide();
			Recaptcha.reload();
			if (!data.key) {
				$("#success").text($("#lang").attr("data-langinsert")).fadeIn().delay(5000).fadeOut();
				return;
			}
			if (response.website) {
				response.name = $("<a/>").attr("href", response.website).text(response.name);
			}
			var $el = $("<blockquote/>")
				.attr("data-messageid", response.id)
				.append($("<cite/>").html(response.name))
				.append((response.email ? $("<span/>").text(" ").append($("<a/>").attr("href", "mailto:" + response.email).text(response.email).addClass("email")) : ""))
				.append($("<time/>").text(response.date))
				.append($("<div/>").html(response.message))
				.append($(".controls:first").clone(true, true).find(".approveMessage").remove().end().find("a").removeAttr("href").end());
			if (data.id) {
				$("blockquote[data-messageid=" + data.id + "]").before($el).remove();
			}
			else {
				$("blockquote:first").before($el);
			}
		})
		.fail(function() {
			$("#error").text($("#lang").attr("data-langerror")).fadeIn().delay(1000).fadeOut();
			Recaptcha.reload();
		});
	});
	
	$("#navAdd").click(function(e) {
		e.preventDefault();
		
		$("#messageForm").show().attr("data-method", "insert");
		$("#messageForm").scrollIntoView();
	});
	
	function updateMessage(e) {
		var blockquote = $(e.currentTarget).parent();
		
		e.preventDefault();
		
		$("#messageForm [name=name]").val(blockquote.find("cite").text());
		$("#messageForm [name=email]").val(blockquote.find(".email").text());
		$("#messageForm [name=website]").val(blockquote.find("cite a").attr("href") || "");
		$("#messageForm [name=message]").val(blockquote.find("div").html().replace(/<br>/gi, "\n").replace(/<\/p><p>/gi, "\n\n").replace("<p>", "").replace("</p>", ""));
		$("#messageForm [name=id]").val(blockquote.attr("data-messageid"));
		
		$("#messageForm").show().attr("data-method", "update");
		document.getElementById("messageForm").scrollIntoView();
	}

	function approveMessage(e) {
		e.preventDefault();
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $("#data").attr("data-baseurl") + "api/message",
			data: { key: $("#data").attr("data-key"), method: "approve", id : $(e.currentTarget).parent().attr("data-messageid") }
		})
		.done(function() {
			$(e.target).remove();
			if ($(".approveMessage").size() === 0)
			{
				$("#approveAll").remove();
			}
		})
		.fail(function() {
			$("#error").text($("#lang").attr("data-langerror")).fadeIn().delay(1000).fadeOut();
		});
	}
	
	function deleteMessage(e) {
		e.preventDefault();
		
		if (confirm($("#lang").attr("data-langdelete")) === true) {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: $("#data").attr("data-baseurl") + "api/message",
				data: { key: $("#data").attr("data-key"), method: "delete", id: $(e.currentTarget).parent().attr("data-messageid") }
			})
			.done(function() {
				$(e.currentTarget).parent().remove();
			})
			.fail(function() {
				$("#error").text($("#lang").attr("data-langerror")).fadeIn().delay(1000).fadeOut();
			});
		}
	}
	
	$(".controls").on("click", function(e) {
		if ($(e.target).hasClass("approveMessage")) {
			approveMessage(e);
		}
		else if ($(e.target).hasClass("updateMessage")) {
			updateMessage(e);
		}
		else if ($(e.target).hasClass("deleteMessage")) {
			deleteMessage(e);
		}
	});
});