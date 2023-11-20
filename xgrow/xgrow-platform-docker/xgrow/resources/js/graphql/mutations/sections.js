/** USED FOR AXIOS */
export const DELETE_SECTIONS_AXIOS = `
mutation($id: String!) {
    deleteSection(id: $id) {
		id,
		platform_id
	}
}`;

export const CREATE_SECTIONS_AXIOS = `
mutation(
    $title: String!
    $thumb_vertical: String! = "https://las.xgrow.com/background-default.png"
    $thumb_horizontal: String! = "https://las.xgrow.com/background-default.png"
    $section_items: [CreateSectionItemInputType!]!
) {
    createSection(
        data: {
            title: $title,
            thumb_vertical: $thumb_vertical,
            thumb_horizontal: $thumb_horizontal,
            published: false,
            section_items: $section_items
        }
    ) {
		id,
		platform_id
	}
}`;

export const UPDATE_SECTIONS_AXIOS = `
mutation(
    $id: String!
    $title: String
    $thumb_vertical: String = "https://las.xgrow.com/background-default.png"
    $thumb_horizontal: String = "https://las.xgrow.com/background-default.png"
    $published: Boolean
    $description: String
    $table_data: String
    $section_items: [CreateSectionItemInputType!]
) {
    updateSection(
        id: $id
        data: {
            title: $title
            thumb_vertical: $thumb_vertical
            thumb_horizontal: $thumb_horizontal
            published: $published
            description: $description
            table_data: $table_data
            section_items: $section_items
        }
    ) {
		id,
		platform_id
	}
}`;


export const UPDATE_SECTIONS_STATUS_AXIOS = `
mutation(
    $id: String!
    $published: Boolean
) {
    updateSection(
        id: $id
        data: {
            published: $published
        }
    ) {
		id,
		platform_id
	}
}`;
