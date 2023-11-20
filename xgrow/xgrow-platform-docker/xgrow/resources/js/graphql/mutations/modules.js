/** USED FOR AXIOS */
export const SAVE_MODULE_MUTATION_AXIOS = `
mutation(
    $name: String!
    $description: String
    $position: Int! = 0
    $status: Boolean!
    $course_id: String!
    $diagram: String
    $started_at: DateTime
    $frequency: Int = 0
    $delivered_at: DateTime
    ) {
    module: createModule(
        data: {
            name: $name
            description: $description
            position: $position
            status: $status
            course_id: $course_id
            diagram: $diagram
            started_at: $started_at
            frequency: $frequency
            delivered_at: $delivered_at
        }
    ) {
        id
        name
        description
        diagram
        created_at
        position
        status
        started_at
        form_delivery
        frequency
        delivery_model
        delivered_at
        course_id
        platform_id
    }
}`;


export const UPDATE_MODULE_MUTATION_AXIOS = `
mutation(
  $id: String!
  $position: Int
  $status: Boolean
  $course_id: String
  $name: String!
  $description: String
  $diagram: String
  $started_at: DateTime
  $frequency: Float = 0
  $delivered_at: DateTime
) {
  module: updateModuleById(
    id: $id
    data: {
      name: $name
      position: $position
      status: $status
      course_id: $course_id
      description: $description
      diagram: $diagram
      started_at: $started_at
      frequency: $frequency
      delivered_at: $delivered_at
    }
  ) {
    id
    name
    description
    diagram
    created_at
    position
    status
    started_at
    form_delivery
    frequency
    delivery_model
    delivered_at
    course_id
    platform_id
  }
}`;

export const UPDATE_MODULE_STATUS_MUTATION_AXIOS = `
mutation($id: String!, $name: String!, $status: Boolean) {
    module: updateModuleById(
        id: $id
        data: { name: $name, status: $status }
    ) {
        id
        name
    }
}`;

export const UPDATE_MODULE_NAME_MUTATION_AXIOS = `
mutation($id: String!, $name: String!) {
    module: updateModuleById(
        id: $id
        data: { name: $name }
    ) {
        id
        name
    }
}`;

export const UPDATE_MODULE_ORDER_MUTATION_AXIOS = `
mutation($id: String!, $name: String!, $position: Int) {
    module: updateModuleById(
        id: $id
        data: { name: $name, position: $position }
    ) {
        id
        name
    }
}`;

export const DELETE_MODULE_MUTATION_AXIOS = `
mutation($id: String!) {
    module: deleteModuleById(id: $id) {
        id
        name
    }
}`;
