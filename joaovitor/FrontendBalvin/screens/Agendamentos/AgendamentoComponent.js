import { StyleSheet, Text, View, ScrollView, TouchableOpacity, Image, } from 'react-native'
import React from 'react'
import Solicitacaoagen from '../Cards/Solicitacaoagen'

const AgendamentoComponent = () => {

    let data = [
        {
            id: 1,
            username: 'Balvin',
            profile_image: "https://th.bing.com/th/id/OIP.Km7S0jP80f6Qn2LMrfKLRQHaFd?pid=ImgDet&rs=1",
          
        },
       
    ]
        
    

    // console.log(data[0].username)
    return (
        <ScrollView style={styles.container}>

            {
                data.map((item) => {
                    return (
                        <Solicitacaoagen
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

export default AgendamentoComponent

const styles = StyleSheet.create({
    container: {
        width: '100%',
        flexDirection: 'column',
    }
})