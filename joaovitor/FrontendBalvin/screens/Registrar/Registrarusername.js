import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator, StatusBar} from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';

const Registrarusername = ({ navigation, route }) => {
    const { email } = route.params
    const [nome, setNome] = useState('')

    const [loading, setLoading] = useState(false)


    const handleUsername = () => {
        if (nome == '') {
            alert('Por favor entre com um nome de usuário')
        }
        else {
            setLoading(true)
            fetch('http://192.168.0.54:3000/mudeonome', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    nome: nome
                })
            })
                .then(res => res.json()).then(
                    data => {
                        if (data.message === "Nome de usuário disponível") {
                            setLoading(false)
                            navigation.navigate('Registrarnovasenha', { email: email, nome: nome })
                        }
                        else {
                            setLoading(false)
                        }
                    }
                ).catch(err => {
                    console.log(err)
                })

        }


    }

    return (
        <View style={{ width: '100%', height: '100%'}}>
        <StatusBar hidden={true}/>
       <TouchableOpacity onPress={() => navigation.navigate('Login')} >

          <Image style={{alignSelf: 'flex-end', marginTop: 20, marginEnd: 20, width: 20, height: 20}} source={require('../../images/saircards.png')}/>

       </TouchableOpacity>
         
       <View style={{width: '100%', justifyContent: 'center', alignItems: 'center', marginTop: 50}}>
            
            <Text  style={{fontSize: 25, fontWeight: 'bold'}}>Crie um novo usuário</Text>
            

            <TextInput style={{width: '95%', height: 40, borderColor: 'gray', borderWidth: 1, borderRadius: 5, marginTop: 20, backgroundColor: 'white',
            padding: 10}}
            
            placeholder="nome de usuário" 
                onChangeText={(text) => setNome(text)}
            />

            {
                loading ? 
                <ActivityIndicator size="large" color="white" style={{marginTop: 20}} />
                :
                <TouchableOpacity  style={{width: '95%', height: 40, borderRadius: 5, marginTop: 20, backgroundColor: '#ec230d',
                color: 'white', fontSize: 18, alignItems: 'center', justifyContent: 'center'}} onPress={() => handleUsername()}>
                <Text style={{fontSize: 15, color: 'white', fontWeight: 'bold'}}
                    
                >
                    Avançar
                </Text>
                </TouchableOpacity>
            }
        </View>
        </View>
    )
}




export default Registrarusername

const styles = StyleSheet.create({})