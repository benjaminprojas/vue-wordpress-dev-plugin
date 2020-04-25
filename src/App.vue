<template>
	<div
		class="wp-vue-test"
		:class="{
			editing: showEdit
		}"
	>
		<div
			v-if="!showEdit"
		>
			<a
				class="dashicons dashicons-edit wp-vue-quickedit"
				title="Edit"
				@click.prevent="edit"
			>
			</a>
			<strong>
				<span id='wp-vue-<?php echo $columnName; ?>-<?php echo $postId; ?>-value'>
					{{ value || 'No Value' }}
				</span>
			</strong>
		</div>
		<div
			v-if="showEdit"
		>
			<textarea
				v-model="value"
				class="wp-vue-quickedit-input"
				rows="4"
				columns="32"
			/>
			<a
				class="dashicons dashicons-yes-alt wp-vue-quickedit-input-save"
				@click.prevent="save"
				title="Save"
			></a>
			<a
				class="dashicons dashicons-dismiss wp-vue-quickedit-input-cancel"
				@click.prevent="cancel"
				title="Cancel"
			></a>
		</div>
	</div>
</template>

<script>
export default {
	props : {
		post    : Object,
		restUrl : String
	},
	data () {
		return {
			value    : null,
			showEdit : false
		}
	},
	methods : {
		save () {
			this.showEdit   = false
			this.post.value = this.value
			this.$http.post('http://wp-vue.local/wp-admin/admin-ajax.php?action=wp_vue_ajax_save_post_meta')
				.send({
					postId      : this.post.id,
					value       : this.value.trim(),
					key         : this.post.columnName,
					_ajax_nonce : this.post.nonce
				})
				.then(() => {})
				.catch(error => {
					console.log(error)
					console.log(`Request to update ${this.post.columnName} failed.`)
				})
		},
		cancel () {
			this.value    = this.post.value
			this.showEdit = false
		},
		edit () {
			this.showEdit = true
		}
	},
	mounted () {
		this.value = this.post.value
	}
}
</script>

<style lang="scss">
.wp-vue-meta-options {
	float: left;
	display: block;
	opacity: 1;
	max-height: 75px;
	overflow: hidden;
	width: 100%;

	&.editing {
		max-height: initial;
		overflow: visible;
	}

	.dashicons {
		cursor: pointer;
	}

	.wp-vue-quickedit {
		margin-right: 5px;
		color: #72777c;

		&:hover {
			color: #0073aa;
			outline: 0;
		}
	}

	.wp-vue-quickedit-input {
		float:left;
		position:relative;
		margin-bottom: 10px;
		font-size:13px;
		width:100%;
		z-index:1;
	}

	.wp-vue-quickedit-input-save {
		margin-right: 5px;
		color: rgb(22, 204, 22);
	}

	.wp-vue-quickedit-input-cancel {
		color: red;
	}

	.wp-vue-quickedit:focus,
	.wp-vue-quickedit-input-save:focus,
	.wp-vue-quickedit-input-cancel:focus  {
		box-shadow: none;
	}

	.wp-vue-quickedit-spinner {
		float:left;
		width:20px;
		margin-right:5px;
	}
}

td.seotitle.column-seotitle,
td.seodesc.column-seodesc,
td.seokeywords.column-seokeywords {
	overflow: visible;
}

@media screen and (max-width: 782px) {
	body.wp-admin {
		th.column-seotitle,
		th.column-seodesc,
		th.column-seokeywords,
		td.seotitle.column-seotitle,
		td.seodesc.column-seodesc,
		td.seokeywords.column-seokeywords {
			display: none;
		}
	}
}
</style>