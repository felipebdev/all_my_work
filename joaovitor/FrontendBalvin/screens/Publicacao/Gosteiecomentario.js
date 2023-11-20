import { StyleSheet, Text, View, ScrollView, TouchableOpacity, Image, } from 'react-native'
import React from 'react'
import Publicaocards from '../Cards/Publicacaocards'

const Gosteiecomentario = () => {

    let data = [
        {
            id: 1,
            username: 'Balvindeveloper',
            profile_image: "https://th.bing.com/th/id/OIP.Km7S0jP80f6Qn2LMrfKLRQHaFd?pid=ImgDet&rs=1",
            image: "https://th.bing.com/th/id/OIP.Km7S0jP80f6Qn2LMrfKLRQHaFd?pid=ImgDet&rs=1",
            likes: [
                "jaozitorj"
            ],
            comments: [
                {
                    id: 1,
                    username: 'jaozitorj',
                    comment: 'nice!'
                },
              
            ]
        },
        
    
    ]
        
    

    // console.log(data[0].username)
    return (
        <ScrollView style={styles.container}>

            {
                data.map((item) => {
                    return (
                        <Publicaocards
                            key={item.id}
                            username={item.username}
                            profile_image={item.profile_image}
                            post_pic={item.image}
                            likes={item.likes}
                            comments={item.comments}
                        />
                    )
                })
            }
        </ScrollView>
    )
}

export default Gosteiecomentario

const styles = StyleSheet.create({
    container: {
        width: '100%',
        flexDirection: 'column',
    }
})